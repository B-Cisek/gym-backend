<?php

declare(strict_types=1);

namespace App\Auth\Presentation\Http\Controller\V1;

use App\Auth\Application\Command\Sync\RegisterOwner;
use App\Auth\Presentation\Http\Request\V1\OwnerRegisterRequest;
use App\Shared\Application\Command\Sync\CommandBus;
use App\Shared\Domain\NotFoundException;
use App\Shared\Presentation\Http\Response\JsonResponseFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final readonly class RegisterOwnerController
{
    public function __construct() {}

    #[Route('/auth/owner/register', methods: ['POST'] )]
    public function register(#[MapRequestPayload] OwnerRegisterRequest $request): JsonResponse
    {
        throw new NotFoundException();
        return JsonResponseFactory::created();
//        $command = new RegisterOwner(
//            email: $request->email,
//            password: $request->password,
//        );
//
//        $this->commandBus->dispatch($command);
//
//        return JsonResponseFactory::created();
    }
}
