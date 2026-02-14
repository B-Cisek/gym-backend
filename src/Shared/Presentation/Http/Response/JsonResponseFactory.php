<?php

declare(strict_types=1);

namespace App\Shared\Presentation\Http\Response;

use App\Auth\Application\Service\AuthTokenPair;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class JsonResponseFactory
{
    public function __construct(
        private SerializerInterface $serializer,
    ) {}

    public function success(int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse(data: ['success' => true], status: $status);
    }

    /**
     * @param array<string, string> $headers
     * @param array<string, mixed>  $context
     */
    public function data(mixed $data, int $status = Response::HTTP_OK, array $headers = [], array $context = []): JsonResponse
    {
        $json = $this->serializer->serialize($data, 'json', array_merge([
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        ], $context));

        return new JsonResponse($json, $status, $headers, true);
    }

    public function created(): JsonResponse
    {
        return new JsonResponse(data: ['success' => true], status: Response::HTTP_CREATED);
    }

    public function noContent(): JsonResponse
    {
        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
    }

    public function error(string $message, int $status = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return new JsonResponse(data: ['success' => false, 'message' => $message], status: $status);
    }

    public function signedIn(AuthTokenPair $authTokenPair): JsonResponse
    {
        return new JsonResponse(data: [
            'token' => $authTokenPair->token,
            'refresh_token' => $authTokenPair->refreshToken,
        ], status: Response::HTTP_OK);
    }
}
