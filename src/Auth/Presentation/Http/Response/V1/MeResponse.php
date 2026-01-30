<?php

declare(strict_types=1);

namespace App\Auth\Presentation\Http\Response\V1;

final readonly class MeResponse
{
    /**
     * @param array<string> $roles
     */
    public function __construct(
        public string $id,
        public string $email,
        public array $roles,
    ) {}
}
