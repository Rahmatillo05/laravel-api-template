<?php

use Illuminate\Http\JsonResponse;

function createdResponse(mixed $data = [], string $message = 'Created', string|int $status = 'success'): JsonResponse
{
    return response()->json([
        'message' => $message,
        'status' => $status,
        'data' => $data,
    ], 201);
}

function okResponse(mixed $data = [], string $message = 'ok', string|int $status = 'ok', array $meta_data = []): JsonResponse
{
    return response()->json([
        'message' => $message,
        'status' => $status,
        'data' => $data,
        'per_page' => $meta_data['per_page'] ?? null,
        ...$meta_data,
    ]);
}

function okWithPaginateResponse(mixed $data = []): JsonResponse
{
    return response()->json($data);
}

function badRequestResponse(string $message = 'Bad Request', mixed $data = [], string|int $status = 'bad_request'): JsonResponse
{
    return response()->json([
        'message' => $message,
        'status' => $status,
        'data' => $data,
    ], 400);
}

function invalidData(string $message = 'The given data was invalid.', mixed $data = [], string|int $status = 'invalid_data'): JsonResponse
{
    return response()->json([
        'message' => $message,
        'status' => $status,
        'data' => $data,
    ], 422);
}

function unauthorizedRequestResponse(string $message = 'Unauthorized', mixed $data = [], string|int $status = 'unauthorized'): JsonResponse
{
    return response()->json([
        'message' => $message,
        'status' => $status,
        'data' => $data,
    ], 401);
}

function forbiddenRequestResponse(string $message = 'Forbidden', mixed $data = [], string|int $status = 'forbidden'): JsonResponse
{
    return response()->json([
        'message' => $message,
        'status' => $status,
        'data' => $data,
    ], 403);
}

function notFoundRequestResponse(string $message = 'Not Found', mixed $data = [], string|int $status = 'not_found'): JsonResponse
{
    return response()->json([
        'message' => $message,
        'status' => $status,
        'data' => $data,
    ], 404);
}

function methodNotAllowedRequestResponse(string $message = 'Method Not Allowed', mixed $data = [], string|int $status = 'method_not_allowed'): JsonResponse
{
    return response()->json([
        'message' => $message,
        'status' => $status,
        'data' => $data,
    ], 405);
}

function tooManyRequestsResponse(string $message = 'Too Many Requests', mixed $data = [], string|int $status = 'too_many_requests'): JsonResponse
{
    return response()->json([
        'message' => $message,
        'status' => $status,
        'data' => $data,
    ], 429);
}

function serverErrorResponse(string $message = 'Something went wrong', mixed $data = [], string|int $status = 'server_error'): JsonResponse
{
    return response()->json([
        'message' => $message,
        'status' => $status,
        'data' => $data,
    ], 500);
}

function errorResponse(string $message, mixed $data = [], string|int|null $status = null): JsonResponse
{
    return response()->json([
        'message' => $message,
        'status' => $status,
        'data' => $data,
    ], 500);
}

function postTooLargeResponse(string $message = 'Post Too Large', mixed $data = [], ?int $status = null): JsonResponse
{
    return response()->json([
        'message' => $message,
        'status' => $status,
        'data' => $data,
    ], 413);
}

function successResponse(string $message, mixed $data = [], string|int $status = 'success', int $statusCode = 200): JsonResponse
{
    return response()->json([
        'message' => $message,
        'status' => $status,
        'data' => $data,
    ], $statusCode);
}
