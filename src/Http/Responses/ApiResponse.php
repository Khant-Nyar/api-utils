<?php

namespace KhantNyar\ApiUtils\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;

class ApiResponse implements Responsable
{
    protected int $httpCode;
    protected array $data;
    protected string $errorMessage;

    public function __construct(int $httpCode, array $data = [], string $errorMessage = '')
    {
        if (!(($httpCode >= 200 && $httpCode < 300) || ($httpCode >= 400 && $httpCode < 600))) {
            throw new \RuntimeException($httpCode . ' is not valid');
        }

        $this->httpCode = $httpCode;
        $this->data = $data;
        $this->errorMessage = $errorMessage;
    }

    public function toResponse($request): JsonResponse
    {
        $payload = match (true) {
            $this->httpCode >= 500 => ['error_message' => 'Server error'],
            $this->httpCode >= 400 => ['error_message' => $this->errorMessage],
            $this->httpCode >= 200 => $this->data,
        };

        return response()->json(
            data: $payload,
            status: $this->httpCode,
            options: JSON_UNESCAPED_UNICODE
        );
    }

    public static function ok(array $data): self
    {
        return new static(200, $data);
    }

    public static function created(array $data): self
    {
        return new static(201, $data);
    }

    public static function notFound(string $errorMessage = "Record not found"): self
    {
        return new static(404, [], $errorMessage);
    }

    public static function badRequest(string $errorMessage = "Bad request"): self
    {
        return new static(400, [], $errorMessage);
    }

    public static function unauthorized(string $errorMessage = "Unauthorized"): self
    {
        return new static(401, [], $errorMessage);
    }

    public static function forbidden(string $errorMessage = "Forbidden"): self
    {
        return new static(403, [], $errorMessage);
    }

    public static function internalServerError(string $errorMessage = "Internal server error"): self
    {
        return new static(500, [], $errorMessage);
    }

    public static function customError(int $httpCode, string $errorMessage): self
    {
        return new static($httpCode, [], $errorMessage);
    }
}
