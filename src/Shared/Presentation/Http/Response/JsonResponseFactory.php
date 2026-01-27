<?php

declare(strict_types=1);

namespace App\Shared\Presentation\Http\Response;

use App\Auth\Application\Service\AuthTokenPair;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class JsonResponseFactory
{
    public static function success(int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse(data: ['success' => true], status: $status);
    }

    public static function created(): JsonResponse
    {
        return new JsonResponse(null, Response::HTTP_CREATED);
    }

    public static function noContent(): JsonResponse
    {
        return new JsonResponse(data: null, status: Response::HTTP_OK);
    }

    public static function error(string $message, int $status = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return new JsonResponse(data: ['success' => false, 'message' => $message], status: $status);
    }

    public static function signedIn(AuthTokenPair $authTokenPair): JsonResponse
    {
        return new JsonResponse(data: [
            'token' => $authTokenPair->token,
            'refresh_token' => $authTokenPair->refreshToken
        ], status: Response::HTTP_OK);
    }
}
