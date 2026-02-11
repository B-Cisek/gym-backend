<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Security\EventListener;

use App\Auth\Infrastructure\Doctrine\Entity\User;
use App\Owner\Infrastructure\Doctrine\Repository\OwnerRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: Events::JWT_CREATED)]
final readonly class JWTCreatedListener
{
    public function __construct(private OwnerRepository $ownerRepository) {}

    public function __invoke(JWTCreatedEvent $event): void
    {
        /** @var User $user */
        $user = $event->getUser();

        if (!$user->isOwner()) {
            return;
        }

        $ownerId = $this->ownerRepository->getIdByUserId($user->getId()->toString());

        $payload = $event->getData();
        $payload['owner_id'] = $ownerId;
        $event->setData($payload);
    }
}
