<?php

declare(strict_types=1);

namespace App\Auth\Presentation\Http\Controller\V1;

use App\Auth\Application\Command\Sync\RegisterOwner;
use App\Auth\Application\Service\AuthTokenPairGeneratorInterface;
use App\Auth\Domain\UserAlreadyExistsException;
use App\Auth\Presentation\Http\Request\V1\OwnerRegisterRequest;
use App\Shared\Application\Command\Sync\CommandBus;
use App\Shared\Presentation\Http\Response\JsonResponseFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final readonly class RegisterOwnerController
{
    public function __construct(
        private CommandBus $commandBus,
        private AuthTokenPairGeneratorInterface $authTokenPairGenerator,
        private JsonResponseFactory $responseFactory,
    ) {}

    #[Route('/auth/owner/register', methods: ['POST'])]
    public function register(#[MapRequestPayload] OwnerRegisterRequest $request): JsonResponse
    {
        try {
            $userId = $this->commandBus->dispatch(new RegisterOwner(
                email: $request->email,
                password: $request->password,
            )->toRegisterUser());

            $tokenPair = $this->authTokenPairGenerator->generateFor($userId);

            return $this->responseFactory->signedIn($tokenPair);
        } catch (UserAlreadyExistsException $e) {
            return $this->responseFactory->error($e->getMessage(), $e->getHttpStatusCode());
        }
    }
}
