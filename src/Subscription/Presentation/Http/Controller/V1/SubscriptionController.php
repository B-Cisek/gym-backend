<?php

declare(strict_types=1);

namespace App\Subscription\Presentation\Http\Controller\V1;

use App\Shared\Infrastructure\Security\AuthContext;
use App\Shared\Presentation\Http\Response\JsonResponseFactory;
use App\Subscription\Application\Query\GetSubscriptionInfo;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final readonly class SubscriptionController
{
    public function __construct(
        private JsonResponseFactory $responseFactory,
        private AuthContext $authContext,
    ) {}

    #[Route(path: '/subscription', name: 'subscription.get', methods: ['GET'])]
    public function __invoke(GetSubscriptionInfo $query): JsonResponse
    {
        return $this->responseFactory->data($query->execute($this->authContext->getOwnerId()));
    }
}
