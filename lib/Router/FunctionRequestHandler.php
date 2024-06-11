<?php

namespace Lib\Router;

use Lib\RequestInterface;
use Lib\ResponseInterface;
use Stringable;

class FunctionRequestHandler implements RequestHandlerInterface, Stringable
{
    /**
     * @var callable
     */
    private $handler;

    private string $name = '';

    public static function create(callable $handler): self
    {
        return new self($handler);
    }

    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    public function withName(string $name): static
    {
        $obj = clone $this;
        $obj->name = $name;

        return $obj;
    }

    /** @psalm-suppress MixedInferredReturnType */
    public function handle(RequestInterface $request): ResponseInterface
    {
        /** @psalm-suppress MixedReturnStatement */
        return call_user_func($this->handler, $request);
    }

    public function __toString(): string
    {
        return $this->name !== '' ? $this->name : get_class($this);
    }
}
