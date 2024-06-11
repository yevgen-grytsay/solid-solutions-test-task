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
        return rtrim((string) ($this->server['PATH_INFO'] ?? '/'), '/');
    }

    public static function createFromGlobals(): self
    {
        return new self($_GET, $_POST, $_SERVER);
    }

    public function getMethod(): string
    {
        return (string) $this->server['REQUEST_METHOD'];
    }
}
