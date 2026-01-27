<?php

declare(strict_types=1);

namespace App\Auth\Application\Service;

interface AuthTokenPairGeneratorInterface
{
    public function generateFor(string $userId): AuthTokenPair;
}
