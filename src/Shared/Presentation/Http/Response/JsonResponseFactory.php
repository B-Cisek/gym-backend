<?php

declare(strict_types=1);

namespace App\Shared\Presentation\Http\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

final class JsonResponseFactory
{
    public static function success(?string $uuid = null, int $status = 200): JsonResponse
    {
        $data = ['success' => true];

        if ($uuid !== null) {
            $data['uuid'] = $uuid;
        }

        return new JsonResponse($data, $status);
    }

    public static function created(?string $uuid = null): JsonResponse
    {
        return self::success($uuid, 201);
    }

    public static function noContent(): JsonResponse
    {
        return new JsonResponse(null, 204);
    }
}
