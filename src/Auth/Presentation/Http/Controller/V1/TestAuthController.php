<?php

declare(strict_types=1);

namespace App\Auth\Presentation\Http\Controller\V1;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final readonly class TestAuthController
{
    #[Route('/test', name: 'test')]
    public function test(): JsonResponse
    {
        return new JsonResponse([
            'message' => 'CONTENT ONLY FOR AUTHENTICATED USERS'
        ]);
    }
}
