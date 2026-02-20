<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\AbstractPaginator;

class ApiResponse
{
    /**
     * Success response
     */
    public static function success(
        mixed $data = null,
        string $message = 'Success',
        int $status = 200,
        array $meta = []
    ): JsonResponse {

        // If Laravel Resource
        if ($data instanceof JsonResource) {
            $data = $data->resolve();
        }

        // If paginator (important جداً)
        if ($data instanceof AbstractPaginator) {
            $meta = array_merge($meta, [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]);

            $data = $data->items();
        }

        // If Arrayable (Collection / Model)
        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        return response()->json([
            'success' => true,
            'status' => $status,
            'message' => $message,
            'data' => $data,
            'errors' => null,
        ], $status);
    }

    /**
     * Error response
     */
    public static function error(
        string $message = 'Error',
        int $status = 400,
        mixed $errors = null
    ): JsonResponse {

        return response()->json([
            'success' => false,
            'status' => $status,
            'message' => $message,
            'data' => null,
            'errors' => $errors,
        ], $status);
    }

    /**
     * Unauthorized (401)
     */
    public static function unauthorized(
        string $message = 'Unauthenticated'
    ): JsonResponse {
        return self::error($message, 401);
    }

    /**
     * Forbidden (403)
     */
    public static function forbidden(
        string $message = 'Forbidden'
    ): JsonResponse {
        return self::error($message, 403);
    }

    /**
     * Not Found (404)
     */
    public static function notFound(
        string $message = 'Resource not found'
    ): JsonResponse {
        return self::error($message, 404);
    }

    /**
     * Server Error (500)
     */
    public static function serverError(
        string $message = 'Server Error'
    ): JsonResponse {
        return self::error($message, 500);
    }
}
