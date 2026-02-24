<?php

declare(strict_types=1);

namespace App\Subscription\Presentation\Http\Controller\V1;

use App\Shared\Presentation\Http\Response\JsonResponseFactory;
use App\Subscription\Application\Query\GetPlans;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final readonly class PlansController
{
    public function __construct(
        private JsonResponseFactory $responseFactory,
    ) {}

    #[Route(path: '/plans', name: 'plans.get', methods: ['GET'])]
    public function __invoke(GetPlans $query): JsonResponse
    {
        return $this->responseFactory->data($query->execute());
    }
}
