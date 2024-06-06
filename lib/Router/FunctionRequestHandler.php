<?php

namespace Lib\Router;

use Lib\Request;
use Lib\Response;

class FunctionRequestHandler implements RequestHandlerInterface
{
    /**
     * @var callable
     */
    private $handler;

    public static function create(callable $handler): self
    {
        return new self($handler);
    }

    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    public function handle(Request $request): Response
    {
        return call_user_func($this->handler, $request);
    }
}
