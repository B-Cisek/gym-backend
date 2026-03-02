<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Security\EventListener;

use App\Auth\Domain\User;
use App\Owner\Domain\OwnerRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: Events::JWT_CREATED)]
final readonly class JWTCreatedListener
{
    public function __construct(
        private OwnerRepository $repository
    ) {}

    public function __invoke(JWTCreatedEvent $event): void
    {
        /** @var User $user */
        $user = $event->getUser();

        if (!$user->isOwner()) {
            return;
        }

        $owner = $this->repository->getByUserId($user->getId());

        $payload = $event->getData();
        $payload['owner_id'] = $owner->id->toString();
        $event->setData($payload);
    }
}
