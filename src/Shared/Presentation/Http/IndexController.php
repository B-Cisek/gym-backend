<?php

declare(strict_types=1);

namespace App\Shared\Presentation\Http;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class IndexController extends AbstractController
{
    #[Route('/', name: 'status.get', methods: ['GET'])]
    public function status(): JsonResponse
    {
        return new JsonResponse(['status' => 'ok']);
    }
}
