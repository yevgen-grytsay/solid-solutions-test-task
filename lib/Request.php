<?php

namespace Lib;

/** @psalm-api  */
class Request implements RequestInterface
{
    public readonly array $get;

    public readonly array $post;

    private array $server;

    /**
     * @param array $get
     * @param array $post
     * @param array $server
     */
    public function __construct(array $get, array $post, array $server)
    {
        $this->get = $get;
        $this->post = $post;
        $this->server = $server;
    }

    public function getPath(): string
    {
        $path = (string) ($this->server['PATH_INFO'] ?? null);
        if ('' === $path) {
            return '/';
        }

        return rtrim($path, '/');
    }

    public static function createFromGlobals(): self
    {
        return new self($_GET, $_POST, $_SERVER);
    }

    public function getMethod(): string
    {
        return (string) $this->server['REQUEST_METHOD'];
    }

    public function getQueryParams(): array
    {
        // parse_str($this->server['QUERY_STRING'] ?? '', $result);
        return $this->get;
    }
    public function isJson(): bool
    {
        return 'application/json' === ($this->server['HTTP_CONTENT_TYPE'] ?? '');
    }

    public function getContent(): string
    {
        return file_get_contents('php://input');
    }

    public function getContentType(): string
    {
        return $this->server['HTTP_CONTENT_TYPE'] ?? 'text/html';
    }
}
