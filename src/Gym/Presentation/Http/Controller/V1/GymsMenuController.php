<?php

declare(strict_types=1);

namespace App\Gym\Presentation\Http\Controller\V1;

use App\Auth\Domain\UserRole;
use App\Gym\Application\Query\GetGymsForMenu;
use App\Shared\Infrastructure\Security\AuthContext;
use App\Shared\Presentation\Http\Response\JsonResponseFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final readonly class GymsMenuController
{
    public function __construct(private JsonResponseFactory $responseFactory) {}

    #[Route('/gyms-for-menu', name: 'gyms.menu.get', methods: ['GET'])]
    #[IsGranted(UserRole::OWNER->value)]
    public function get(GetGymsForMenu $getGyms, AuthContext $authContext): JsonResponse
    {
        $collection = $getGyms->execute($authContext->getOwnerId());

        return $this->responseFactory->data($collection);
    }
}
