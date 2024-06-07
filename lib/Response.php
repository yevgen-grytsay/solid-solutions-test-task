<?php

namespace Lib;

class Response
{
    public const HTTP_OK = 200;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_INTERNAL_SERVER_ERROR = 500;

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
     * @param array $headers
     */
    public function __construct(string $data, int $status = self::HTTP_OK, array $headers = [])
    {
        $this->body = $data;
        $this->headers = $headers;
        $this->status = $status;
    }
}
