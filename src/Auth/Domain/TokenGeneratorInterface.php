<?php

declare(strict_types=1);

namespace App\Auth\Domain;

use App\Shared\Domain\Id;

interface TokenGeneratorInterface
{
    public function generateFor(Id $id): string;
}
