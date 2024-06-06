<?php

namespace Lib;

class Response
{
    public const HTTP_OK = 200;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_INTERNAL_SERVER_ERROR = 500;

    public readonly string $body;
    public readonly array $headers;
    public readonly int $status;

    public static function json(array $data): self
    {
        return new self(json_encode($data), 200, [
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
    public function __construct(string $data, int $status = 200, array $headers = [])
    {
        $this->body = $data;
        $this->headers = $headers;
        $this->status = $status;
    }
}
