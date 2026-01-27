<?php

declare(strict_types=1);

namespace App\Auth\Application\Command\Sync;

use App\Auth\Domain\Email;
use App\Auth\Domain\PasswordHasherInterface;
use App\Auth\Domain\User;
use App\Auth\Domain\UserAlreadyExistsException;
use App\Auth\Domain\UserRegistered;
use App\Auth\Domain\UserRepository;
use App\Shared\Application\Command\Sync\CommandHandler;
use App\Shared\Application\Service\IdGeneratorInterface;
use App\Shared\Domain\EventDispatcher;

final readonly class RegisterUserHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $repository,
        private PasswordHasherInterface $hasher,
        private EventDispatcher $eventDispatcher,
        private IdGeneratorInterface $idGenerator
    ) {}

    /**
     * @throws UserAlreadyExistsException
     */
    public function __invoke(RegisterUser $command): string
    {
        $email = Email::fromString($command->email);

        if ($this->repository->existsByEmail($email)) {
            throw new UserAlreadyExistsException($command->email);
        }

        $user = User::register(
            $this->idGenerator->generate(),
            $email,
            $command->roles
        );

        $this->repository->save($user, $this->hasher->hash($command->password));

        $this->eventDispatcher->dispatch(new UserRegistered($user->id));

        return $user->id->toString();
    }
}
