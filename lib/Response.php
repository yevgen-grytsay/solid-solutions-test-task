<?php

namespace Lib;

use LogicException;

/** @psalm-api  */
class Response
{
    public const HTTP_OK = 200;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_INTERNAL_SERVER_ERROR = 500;

    /**
     * @var array<int, string>
     */
    private static array $statusTexts = [
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        404 => 'Not Found',
        500 => 'Internal Server Error',
    ];

    public readonly string $body;
    /**
     * @var array<string, string>
     */
    public readonly array $headers;
    public readonly int $status;

    public static function json(array $data): self
    {
        return new self(json_encode($data), self::HTTP_OK, [
            'Content-Type' => 'application/json',
        ]);
    }

    public static function jsonError(array $data, int $httpCode): self
    {
        return new self(json_encode($data), $httpCode, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * @param string $data
     * @param int $status
     * @param array<string, string> $headers
     */
    public function __construct(string $data, int $status = self::HTTP_OK, array $headers = [])
    {
        $this->body = $data;
        $this->headers = $headers;
        $this->status = $status;
    }

    public function getStatusText(): string
    {
        if (!array_key_exists($this->status, static::$statusTexts)) {
            throw new LogicException(sprintf("Status text for code %d not implemented", $this->status));
        }

        return static::$statusTexts[$this->status];
    }
}
