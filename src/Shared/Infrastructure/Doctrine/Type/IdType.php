<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Doctrine\Type;

use App\Shared\Domain\Id;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

final class IdType extends Type
{
    public const string NAME = 'id';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getGuidTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Id) {
            return $value->toString();
        }

        throw new ConversionException('Invalid Id value.');
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Id
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Id) {
            return $value;
        }

        if (is_string($value)) {
            return new Id($value);
        }

        throw new ConversionException('Invalid Id value.');
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
