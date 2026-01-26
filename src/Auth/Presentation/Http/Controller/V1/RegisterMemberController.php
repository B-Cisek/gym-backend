<?php

declare(strict_types=1);

namespace App\Auth\Presentation\Http\Controller\V1;

use App\Auth\Application\Command\Sync\RegisterMember;
use App\Auth\Domain\UserAlreadyExistsException;
use App\Auth\Presentation\Http\Request\V1\MemberRegisterRequest;
use App\Shared\Application\Command\Sync\CommandBus;
use App\Shared\Presentation\Http\Response\JsonResponseFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final readonly class RegisterMemberController
{
    public function __construct(private CommandBus $commandBus) {}

    #[Route('/auth/member/register', methods: ['POST'])]
    public function register(#[MapRequestPayload] MemberRegisterRequest $request): JsonResponse
    {
        try {
            $this->commandBus->dispatch(new RegisterMember(
                email: $request->email,
                password: $request->password,
            )->toRegisterUser());
        } catch (UserAlreadyExistsException $e) {
            return JsonResponseFactory::error($e->getMessage(), Response::HTTP_CONFLICT);
        }

        return JsonResponseFactory::created();
    }
}
