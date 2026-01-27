<?php

declare(strict_types=1);

namespace App\Auth\Presentation\Http\Controller\V1;

use App\Auth\Application\Command\Sync\RegisterMember;
use App\Auth\Application\Service\AuthTokenPairGeneratorInterface;
use App\Auth\Domain\UserAlreadyExistsException;
use App\Auth\Infrastructure\Doctrine\Repository\UserRepository;
use App\Auth\Presentation\Http\Request\V1\MemberRegisterRequest;
use App\Shared\Application\Command\Sync\CommandBus;
use App\Shared\Presentation\Http\Response\JsonResponseFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final readonly class RegisterMemberController
{
    public function __construct(
        private CommandBus $commandBus,
        private AuthTokenPairGeneratorInterface $authTokenPairGenerator,
        private UserRepository $userRepository
    ) {}

    #[Route('/auth/member/register', methods: ['POST'])]
    public function register(#[MapRequestPayload] MemberRegisterRequest $request): JsonResponse
    {
        try {
            $this->commandBus->dispatch(new RegisterMember(
                email: $request->email,
                password: $request->password,
            )->toRegisterUser());

            $user = $this->userRepository->getByEmail($request->email);

            if (!$user) {
                throw new \RuntimeException('User with given email not found.', Response::HTTP_NOT_FOUND);
            }

            $tokenPair = $this->authTokenPairGenerator->generateFor($user->getId()->toString());

            return JsonResponseFactory::signedIn($tokenPair);
        } catch (UserAlreadyExistsException $e) {
            return JsonResponseFactory::error($e->getMessage(), Response::HTTP_CONFLICT);
        }
    }
}
