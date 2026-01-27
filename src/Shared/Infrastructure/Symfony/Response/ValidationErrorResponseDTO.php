<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Response;

final readonly class ValidationErrorResponseDTO
{
    /**
     * @param array<string, string[]> $errors
     */
    public function __construct(
        public string $message,
        public array $errors,
    ) {}
}
