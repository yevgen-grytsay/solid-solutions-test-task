<?php

namespace Lib\Router;

use Lib\RequestInterface;
use Lib\ResponseInterface;
use Stringable;

interface RequestHandlerInterface extends Stringable
{
    public function handle(RequestInterface $request): ResponseInterface;
}
