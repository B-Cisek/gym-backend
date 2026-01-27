<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\EventListener;

use App\Auth\Domain\UserRegistered;
use App\Auth\Domain\UserRepository;
use App\Shared\Application\Service\Email;
use App\Shared\Application\Service\Mailer;

final readonly class SendWelcomeEmailListener
{
    public function __construct(
        private UserRepository $userRepository,
        private Mailer $mailer,
    ) {
    }

    public function __invoke(UserRegistered $event): void
    {
        $user = $this->userRepository->get($event->userId);

        $email = new Email(
            recipient: $user->email->value,
            subject: 'Welcome to Gym Management!',
            text: sprintf(
                "Hello!\n\nThank you for registering with Gym Management.\n\nYour account with email %s has been created successfully.\n\nBest regards,\nGym Management Team",
                $user->email->value
            ),
        );

        $this->mailer->send($email);
    }
}
