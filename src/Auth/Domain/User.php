<?php

declare(strict_types=1);

namespace App\Auth\Domain;

use App\Shared\Domain\Id;
use App\Shared\Domain\TimestampTrait;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id as DoctrineId;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[Entity]
#[Table(name: 'users')]
#[UniqueConstraint(name: 'UNIQ_ID', columns: ['id'])]
#[UniqueConstraint(name: 'UNIQ_EMAIL', columns: ['email'])]
final class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampTrait;

    /**
     * @param array<UserRole> $roles
     */
    private function __construct(
        #[DoctrineId]
        #[Column(type: 'id', unique: true)]
        public readonly Id $id,
        #[Column(type: 'email', length: 255, unique: true)]
        private Email $email,
        #[Column(type: 'string', length: 255)]
        private string $password,
        #[Column(type: 'user_roles')]
        private array $roles,
    ) {
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * @param array<UserRole> $roles
     */
    public static function register(Id $id, Email $email, string $password, array $roles): self
    {
        return new self($id, $email, $password, $roles);
    }

    public function getUserIdentifier(): string
    {
        return $this->email->value;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return array<string> $roles
     */
    public function getRoles(): array
    {
        return array_map(fn (UserRole $role) => $role->value, $this->roles);
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function isOwner(): bool
    {
        return in_array(UserRole::OWNER, $this->roles, true);
    }

    public function isMember(): bool
    {
        return in_array(UserRole::MEMBER, $this->roles, true);
    }
}
