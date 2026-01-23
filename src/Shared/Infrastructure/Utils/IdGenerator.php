<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Utils;

use App\Shared\Application\Service\IdGeneratorInterface;
use App\Shared\Domain\Id;
use Symfony\Component\Uid\Uuid;

final class IdGenerator implements IdGeneratorInterface
{
    public function generate(): Id
    {
        return new Id(Uuid::v7()->toString());
    }
}
