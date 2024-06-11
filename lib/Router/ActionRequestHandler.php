<?php

namespace Lib\Router;

use Lib\ActionInterface;
use Lib\RequestInterface;
use Lib\ResponseInterface;
use Stringable;

class ActionRequestHandler implements RequestHandlerInterface
{
    private ActionInterface $action;

    public static function fromAction(ActionInterface $action): ActionRequestHandler
    {
        return new self($action);
    }

    public function __construct(ActionInterface $action)
    {
        $this->action = $action;
    }

    public function __toString(): string
    {
        return $this->action instanceof Stringable
            ? sprintf('ActionRequestHandler: %s', $this->action)
            : sprintf('ActionRequestHandler: %s', get_class($this->action));
    }

    public function handle(RequestInterface $request): ResponseInterface
    {
        return $this->action->index($request);
    }
}
