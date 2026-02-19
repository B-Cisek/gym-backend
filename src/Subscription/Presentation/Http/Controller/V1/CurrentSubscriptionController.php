<?php

declare(strict_types=1);

namespace App\Subscription\Presentation\Http\Controller\V1;

use App\Auth\Domain\UserRole;
use App\Shared\Infrastructure\Security\AuthContext;
use App\Shared\Presentation\Http\Response\JsonResponseFactory;
use App\Subscription\Application\Query\GetCurrentSubscription;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final readonly class CurrentSubscriptionController
{
    public function __construct(
        private AuthContext $authContext,
        private JsonResponseFactory $responseFactory,
    ) {}

    #[Route(path: '/subscriptions/current', name: 'subscriptions.current', methods: ['GET'])]
    #[IsGranted(UserRole::OWNER->value)]
    public function __invoke(GetCurrentSubscription $query): JsonResponse
    {
        $result = $query->execute($this->authContext->getOwnerId());

        return $this->responseFactory->data($result);
    }
}
