<?php

namespace App\Exceptions;

use Exception;

class BroadcastException extends Exception
{
    /**
     * Create a new broadcast exception instance.
     */
    public function __construct(string $message = 'Broadcast error', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Create exception for rate limiting.
     */
    public static function rateLimited(int $retryAfter = 60): self
    {
        return new self(
            "Lichess API rate limited. Please try again in {$retryAfter} seconds.",
            429
        );
    }

    /**
     * Create exception for missing broadcast.
     */
    public static function notFound(string $broadcastId): self
    {
        return new self(
            "Broadcast '{$broadcastId}' not found.",
            404
        );
    }

    /**
     * Create exception for API timeout.
     */
    public static function timeout(string $endpoint): self
    {
        return new self(
            "Request to Lichess API endpoint '{$endpoint}' timed out.",
            503
        );
    }

    /**
     * Create exception for API error.
     */
    public static function apiError(int $statusCode, string $message = ''): self
    {
        return new self(
            "Lichess API returned error {$statusCode}. {$message}",
            $statusCode
        );
    }

    /**
     * Render the exception as JSON.
     */
    public function render()
    {
        return response()->json([
            'message' => $this->message,
            'error' => class_basename(static::class),
        ], $this->code ?: 500);
    }
}
