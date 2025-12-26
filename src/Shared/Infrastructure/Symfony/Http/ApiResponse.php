<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Http;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class ApiResponse
{
    public static function success(mixed $data = null, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        $response = [];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        return new JsonResponse($response, $statusCode);
    }

    public static function created(mixed $data = null): JsonResponse
    {
        return self::success($data, Response::HTTP_CREATED);
    }

    public static function error(string $message, int $statusCode = Response::HTTP_BAD_REQUEST, array $details = []): JsonResponse
    {
        $response = [
            'error' => [
                'message' => $message,
                'timestamp' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
            ]
        ];

        if (!empty($details)) {
            $response['error']['details'] = $details;
        }

        return new JsonResponse($response, $statusCode);
    }

    public static function validationError(array $errors): JsonResponse
    {
        return self::error('Validation failed', Response::HTTP_UNPROCESSABLE_ENTITY, $errors);
    }

    public static function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return self::error($message, Response::HTTP_NOT_FOUND);
    }

    public static function unauthorized(string $message = 'Authentication required'): JsonResponse
    {
        return self::error($message, Response::HTTP_UNAUTHORIZED);
    }

    public static function forbidden(string $message = 'Access denied'): JsonResponse
    {
        return self::error($message, Response::HTTP_FORBIDDEN);
    }
}