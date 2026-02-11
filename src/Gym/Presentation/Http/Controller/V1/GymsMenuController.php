<?php

declare(strict_types=1);

namespace App\Gym\Presentation\Http\Controller\V1;

use App\Gym\Application\Query\GetGymsForMenu;
use App\Shared\Infrastructure\Security\AuthContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class GymsMenuController extends AbstractController
{
    #[Route('/gyms-for-menu', name: 'gyms.menu.get', methods: ['GET'])]
    public function get(GetGymsForMenu $getGyms, AuthContext $authContext): JsonResponse
    {
        $collection = $getGyms->execute($authContext->getOwnerId());

        return $this->json($collection);
    }
}
