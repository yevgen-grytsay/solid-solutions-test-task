<?php

namespace Lib\Router;

use Lib\RequestInterface;
use Lib\ResponseInterface;

interface RequestHandlerInterface
{
    public function handle(RequestInterface $request): ResponseInterface;
}
