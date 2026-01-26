<?php

declare(strict_types=1);

namespace App\Auth\Presentation\Http\Controller\V1;

use App\Auth\Application\Command\Sync\RegisterOwner;
use App\Auth\Domain\Email;
use App\Auth\Domain\TokenGeneratorInterface;
use App\Auth\Domain\UserAlreadyExistsException;
use App\Auth\Domain\UserRepository;
use App\Auth\Presentation\Http\Request\V1\OwnerRegisterRequest;
use App\Shared\Application\Command\Sync\CommandBus;
use App\Shared\Presentation\Http\Response\JsonResponseFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final readonly class RegisterOwnerController
{
    public function __construct(
        private CommandBus $commandBus,
        private TokenGeneratorInterface $tokenGenerator,
        private UserRepository $userRepository
    ) {}

    #[Route('/auth/owner/register', methods: ['POST'])]
    public function register(#[MapRequestPayload] OwnerRegisterRequest $request): JsonResponse
    {
        try {
            $this->commandBus->dispatch(new RegisterOwner(
                email: $request->email,
                password: $request->password,
            )->toRegisterUser());

            $user = $this->userRepository->getByEmail(Email::fromString($request->email));
            $token = $this->tokenGenerator->generateFor($user->id);

            return JsonResponseFactory::signedIn(token: $token);
        } catch (UserAlreadyExistsException $e) {
            return JsonResponseFactory::error($e->getMessage(), Response::HTTP_CONFLICT);
        }
    }
}
