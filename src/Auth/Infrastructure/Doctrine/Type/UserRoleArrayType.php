<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Doctrine\Type;

use App\Auth\Domain\UserRole;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Exception\SerializationFailed;
use Doctrine\DBAL\Types\JsonType;

final class UserRoleArrayType extends JsonType
{
    public const string NAME = 'user_roles';

    /**
     * @throws SerializationFailed
     * @throws ConversionException
     */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): string
    {
        if ($value === null) {
            return parent::convertToDatabaseValue([], $platform);
        }

        if (!is_array($value)) {
            throw new ConversionException('Invalid user roles value (expected array of UserRole).');
        }

        $values = array_map(
            static fn (UserRole $role): string => $role->value,
            $value
        );

        return parent::convertToDatabaseValue($values, $platform);
    }

    /** @return UserRole[] */
    public function convertToPHPValue($value, AbstractPlatform $platform): array
    {
        $decoded = parent::convertToPHPValue($value, $platform);

        if ($decoded === null) {
            return [];
        }

        if (!is_array($decoded)) {
            throw new ConversionException('Invalid JSON value for user roles (expected array).');
        }

        return array_map(static fn (string $role): UserRole => UserRole::from($role), $decoded);
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
