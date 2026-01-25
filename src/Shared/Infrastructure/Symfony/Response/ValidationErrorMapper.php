<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Response;

use Symfony\Component\Validator\ConstraintViolationListInterface;

final readonly class ValidationErrorMapper
{
    public function map(ConstraintViolationListInterface $violations): ValidationErrorResponseDTO
    {
        $errors = [];

        foreach ($violations as $violation) {
            $propertyPath = $violation->getPropertyPath();
            $message = $violation->getMessage();

            if (!isset($errors[$propertyPath])) {
                $errors[$propertyPath] = [];
            }

            $errors[$propertyPath][] = $message;
        }

        return new ValidationErrorResponseDTO(
            message: 'Validation failed',
            errors: $errors,
        );
    }
}
