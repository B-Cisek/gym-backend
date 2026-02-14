<?php

declare(strict_types=1);

namespace App\Gym\Presentation\Http\Controller\V1;

use App\Auth\Domain\UserRole;
use App\Gym\Application\Command\Sync\CreateGym;
use App\Gym\Application\Command\Sync\DeleteGym;
use App\Gym\Application\Command\Sync\UpdateGym;
use App\Gym\Application\Query\GetGyms;
use App\Gym\Presentation\Http\Request\V1\CreateGymRequest;
use App\Gym\Presentation\Http\Request\V1\UpdateGymRequest;
use App\Shared\Application\Command\Sync\CommandBus;
use App\Shared\Domain\DomainException;
use App\Shared\Infrastructure\Security\AuthContext;
use App\Shared\Presentation\Http\Response\JsonResponseFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;

final readonly class GymsController
{
    public function __construct(
        private AuthContext $authContext,
        private CommandBus $syncCommandBus,
        private LoggerInterface $logger,
        private JsonResponseFactory $responseFactory,
    ) {}

    #[Route('/gyms', name: 'gyms.get', methods: ['GET'])]
    #[IsGranted(UserRole::OWNER->value)]
    public function get(GetGyms $getGyms): JsonResponse
    {
        $collection = $getGyms->execute($this->authContext->getOwnerId());

        return $this->responseFactory->data($collection);
    }

    #[Route('/gyms', name: 'gyms.create', methods: ['POST'])]
    #[IsGranted(UserRole::OWNER->value)]
    public function post(#[MapRequestPayload] CreateGymRequest $request): JsonResponse
    {
        try {
            $this->syncCommandBus->dispatch(new CreateGym(
                ownerId: $this->authContext->getOwnerId(),
                name: $request->name,
                street: $request->street,
                city: $request->city,
                postalCode: $request->postalCode,
            ));

            return $this->responseFactory->noContent();
        } catch (\Throwable $e) {
            $this->logger->error(
                message: 'FAILED_TO_CREATE_GYM',
                context: [
                    'ownerId' => $this->authContext->getOwnerId(),
                    'message' => $e->getMessage(),
                    'exception' => $e,
                ]
            );

            return $this->responseFactory->error('Failed to create gym');
        }
    }

    #[Route('/gyms/{id}', name: 'gyms.update', methods: ['PUT'])]
    #[IsGranted(UserRole::OWNER->value)]
    public function update(Uuid $id, #[MapRequestPayload] UpdateGymRequest $request): JsonResponse
    {
        try {
            $this->syncCommandBus->dispatch(new UpdateGym(
                gymId: $id->toString(),
                ownerId: $this->authContext->getOwnerId(),
                name: $request->name,
                street: $request->street,
                city: $request->city,
                postalCode: $request->postalCode,
            ));

            return $this->responseFactory->noContent();
        } catch (DomainException $e) {
            return $this->responseFactory->error($e->getMessage(), $e->getHttpStatusCode());
        } catch (\Throwable $e) {
            $this->logger->error(
                message: 'FAILED_TO_UPDATE_GYM',
                context: [
                    'ownerId' => $this->authContext->getOwnerId(),
                    'message' => $e->getMessage(),
                    'exception' => $e,
                ]
            );

            return $this->responseFactory->error('Failed to update gym');
        }
    }

    #[Route('/gyms/{id}', name: 'gyms.delete', methods: ['DELETE'])]
    #[IsGranted(UserRole::OWNER->value)]
    public function delete(Uuid $id): JsonResponse
    {
        try {
            $this->syncCommandBus->dispatch(new DeleteGym(
                gymId: $id->toString(),
                ownerId: $this->authContext->getOwnerId(),
            ));

            return $this->responseFactory->noContent();
        } catch (DomainException $e) {
            return $this->responseFactory->error($e->getMessage(), $e->getHttpStatusCode());
        } catch (\Throwable $e) {
            $this->logger->error(
                message: 'FAILED_TO_DELETE_GYM',
                context: [
                    'ownerId' => $this->authContext->getOwnerId(),
                    'message' => $e->getMessage(),
                    'exception' => $e,
                ]
            );

            return $this->responseFactory->error('Failed to delete gym');
        }
    }
}
