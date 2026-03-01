<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Security\EventListener;

use App\Auth\Domain\User;
use App\Owner\Domain\Owner;
use App\Shared\Domain\Id;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: Events::JWT_CREATED)]
final readonly class JWTCreatedListener
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function __invoke(JWTCreatedEvent $event): void
    {
        /** @var User $user */
        $user = $event->getUser();

        if (!$user->isOwner()) {
            return;
        }

        $ownerId = $this->getIdByUserId($user->getId());

        $payload = $event->getData();
        $payload['owner_id'] = $ownerId;
        $event->setData($payload);
    }

    private function getIdByUserId(Id $userId): string
    {
        $qb = $this->entityManager->createQueryBuilder();

        return $qb->select('o.id')
            ->from(Owner::class, 'o')
            ->where('o.userId = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
