<?php

declare(strict_types=1);

namespace App\Tests\Unit\Auth\Application\Command\Sync;

use App\Auth\Application\Command\Sync\RegisterUser;
use App\Auth\Application\Command\Sync\RegisterUserHandler;
use App\Auth\Domain\Email;
use App\Auth\Domain\PasswordHasherInterface;
use App\Auth\Domain\User;
use App\Auth\Domain\UserAlreadyExistsException;
use App\Auth\Domain\UserRegistered;
use App\Auth\Domain\UserRepository;
use App\Auth\Domain\UserRole;
use App\Shared\Application\Service\IdGeneratorInterface;
use App\Shared\Domain\EventDispatcher;
use App\Shared\Domain\Id;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class RegisterUserHandlerTest extends TestCase
{
    private const string USER_ID = '550e8400-e29b-41d4-a716-446655440000';
    private const string EMAIL = 'john@example.com';
    private const string PASSWORD = 'secret123';
    private const string HASHED_PASSWORD = 'hashed_secret123';

    #[Test]
    public function it_registers_user_and_dispatches_event(): void
    {
        $id = new Id(self::USER_ID);

        $repository = $this->createMock(UserRepository::class);
        $repository->method('existsByEmail')->willReturn(false);
        $repository
            ->expects(self::once())
            ->method('save')
            ->with(
                self::callback(fn (User $user): bool => $user->id->equals($id)
                    && $user->email->equals(Email::fromString(self::EMAIL))
                    && $user->roles === [UserRole::MEMBER]),
                self::HASHED_PASSWORD,
            )
        ;

        $hasher = $this->createStub(PasswordHasherInterface::class);
        $hasher->method('hash')->willReturn(self::HASHED_PASSWORD);

        $eventDispatcher = $this->createMock(EventDispatcher::class);
        $eventDispatcher
            ->expects(self::once())
            ->method('dispatch')
            ->with(self::callback(
                fn (UserRegistered $event): bool => $event->userId->equals($id)
            ))
        ;

        $idGenerator = $this->createStub(IdGeneratorInterface::class);
        $idGenerator->method('generate')->willReturn($id);

        $handler = new RegisterUserHandler($repository, $hasher, $eventDispatcher, $idGenerator);

        $result = ($handler)(new RegisterUser(
            email: self::EMAIL,
            password: self::PASSWORD,
            roles: [UserRole::MEMBER],
        ));

        self::assertSame(self::USER_ID, $result);
    }

    #[Test]
    public function it_throws_when_user_already_exists(): void
    {
        $repository = $this->createMock(UserRepository::class);
        $repository->method('existsByEmail')->willReturn(true);
        $repository->expects(self::never())->method('save');

        $eventDispatcher = $this->createMock(EventDispatcher::class);
        $eventDispatcher->expects(self::never())->method('dispatch');

        $handler = new RegisterUserHandler(
            $repository,
            $this->createStub(PasswordHasherInterface::class),
            $eventDispatcher,
            $this->createStub(IdGeneratorInterface::class),
        );

        $this->expectException(UserAlreadyExistsException::class);

        ($handler)(new RegisterUser(
            email: self::EMAIL,
            password: self::PASSWORD,
            roles: [UserRole::MEMBER],
        ));
    }
}
