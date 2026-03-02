<?php

declare(strict_types=1);

namespace App\Owner\Infrastructure\Query;

use App\Owner\Application\Query\GetOwnerSettings;
use App\Owner\Application\Query\Result\OwnerSettings;
use App\Owner\Domain\Owner;
use App\Owner\Domain\OwnerNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

final readonly class GetOwnerSettingsQuery implements GetOwnerSettings
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function execute(string $ownerId): OwnerSettings
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $result = $queryBuilder
            ->select(
                'o.companyName',
                'o.taxId',
                'o.firstName',
                'o.lastName',
                'o.email',
                'o.phone',
                'o.address.street',
                'o.address.city',
                'o.address.postalCode',
            )
            ->from(Owner::class, 'o')
            ->where('o.id = :ownerId')
            ->setParameter('ownerId', $ownerId)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if ($result === null) {
            throw new OwnerNotFoundException();
        }

        return new OwnerSettings(
            firstName: $result['firstName'],
            lastName: $result['lastName'],
            email: $result['email'],
            phone: $result['phone'],
            companyName: $result['companyName'],
            taxId: $result['taxId'],
            street: $result['address.street'],
            city: $result['address.city'],
            postalCode: $result['address.postalCode'],
        );
    }
}
