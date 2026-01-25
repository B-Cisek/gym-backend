<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Response;

final readonly class ErrorResponseDTO
{
    public function __construct(public string $message) {}
}
