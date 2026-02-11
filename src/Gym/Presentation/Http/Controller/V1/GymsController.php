<?php

declare(strict_types=1);

namespace App\Gym\Presentation\Http\Controller\V1;

use App\Gym\Application\Command\Sync\CreateGym;
use App\Gym\Application\Command\Sync\DeleteGym;
use App\Gym\Application\Command\Sync\UpdateGym;
use App\Gym\Application\Query\GetGyms;
use App\Gym\Presentation\Http\Request\V1\CreateGymRequest;
use App\Gym\Presentation\Http\Request\V1\UpdateGymRequest;
use App\Shared\Application\Command\Sync\CommandBus;
use App\Shared\Infrastructure\Security\AuthContext;
use App\Shared\Presentation\Http\Response\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

final class GymsController extends AbstractController
{
    public function __construct(
        private readonly AuthContext $authContext,
        private readonly CommandBus $syncCommandBus,
    ) {}

    #[Route('/gyms', name: 'gyms.get', methods: ['GET'])]
    public function get(GetGyms $getGyms): JsonResponse
    {
        $collection = $getGyms->execute($this->authContext->getOwnerId());

        return $this->json($collection);
    }

    #[Route('/gyms', name: 'gyms.create', methods: ['POST'])]
    public function post(#[MapRequestPayload] CreateGymRequest $request): JsonResponse
    {
        try {
            $this->syncCommandBus->dispatch(new CreateGym(
                ownerId: $this->authContext->getOwnerId(),
                name: $request->name,
                street: $request->street,
                city: $request->city,
                postalCode: $request->postalCode,
                voivodeship: $request->voivodeship
            ));

            return JsonResponseFactory::noContent();
        } catch (\Throwable $e) {
            return JsonResponseFactory::error($e->getMessage());
        }
    }

    #[Route('/gyms/{id}', name: 'gyms.update', methods: ['PUT'])]
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
                voivodeship: $request->voivodeship
            ));

            return JsonResponseFactory::noContent();
        } catch (\Throwable $e) {
            return JsonResponseFactory::error($e->getMessage());
        }
    }

    #[Route('/gyms/{id}', name: 'gyms.delete', methods: ['DELETE'])]
    public function delete(Uuid $id): JsonResponse
    {
        try {
            $this->syncCommandBus->dispatch(new DeleteGym(
                gymId: $id->toString(),
                ownerId: $this->authContext->getOwnerId(),
            ));

            return JsonResponseFactory::noContent();
        } catch (\Throwable $e) {
            return JsonResponseFactory::error($e->getMessage());
        }
    }
}
