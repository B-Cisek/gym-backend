<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Listener;

use App\Shared\Domain\DomainException;
use App\Shared\Domain\NotFoundException;
use App\Shared\Infrastructure\Symfony\Response\ValidationErrorMapper;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final readonly class ExceptionListener
{
    public function __construct(
        private ValidationErrorMapper $validationMapper,
        private string $environment,
    ) {}

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($this->isValidationException($exception)) {
            $response = $this->handleValidationException($exception);
            $event->setResponse($response);

            return;
        }

        if ($exception instanceof DomainException) {
            $response = $this->handleDomainException($exception);
            $event->setResponse($response);

            return;
        }

        if ($exception instanceof NotFoundException) {
            $response = $this->handleNotFoundException($exception);
            $event->setResponse($response);

            return;
        }

        // TODO: Handle AccessDeniedException and rest of lexik_jwt_authentication exceptions
        if ($exception instanceof AccessDeniedException) {
            $response = new JsonResponse(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
            $event->setResponse($response);
            return;
        }

        $response = $this->handleGenericException($exception);
        $event->setResponse($response);
    }

    private function isValidationException(\Throwable $exception): bool
    {
        if (!$exception instanceof HttpException) {
            return false;
        }

        $previous = $exception->getPrevious();

        return $previous instanceof ValidationFailedException;
    }

    private function handleValidationException(\Throwable $exception): JsonResponse
    {
        /** @var ValidationFailedException $validationException */
        $validationException = $exception->getPrevious();
        $violations = $validationException->getViolations();

        $dto = $this->validationMapper->map($violations);

        return new JsonResponse([
            'message' => $dto->message,
            'errors' => $dto->errors,
        ], 422);
    }

    private function handleDomainException(DomainException $exception): JsonResponse
    {
        $data = [
            'message' => $exception->getMessage(),
        ];

        $this->addStackTraceToResponse($data, $exception);

        return new JsonResponse($data, $exception->getHttpStatusCode());
    }

    private function handleNotFoundException(NotFoundException $exception): JsonResponse
    {
        $data = [
            'message' => $exception->getMessage(),
        ];

        $this->addStackTraceToResponse($data, $exception);

        return new JsonResponse($data, 404);
    }

    private function handleGenericException(\Throwable $exception): JsonResponse
    {
        $data = [
            'message' => $this->environment === 'dev'
                ? $exception->getMessage()
                : 'Internal server error',
        ];

        $this->addStackTraceToResponse($data, $exception);

        return new JsonResponse($data, 500);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function addStackTraceToResponse(array &$data, \Throwable $exception): void
    {
        if ($this->environment === 'dev') {
            $data['trace'] = $this->getStackTrace($exception);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function getStackTrace(\Throwable $exception): array
    {
        return [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ];
    }
}
