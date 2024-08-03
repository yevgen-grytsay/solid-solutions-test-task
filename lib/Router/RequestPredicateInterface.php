<?php

namespace Lib\Router;

use Lib\RequestInterface;

interface RequestPredicateInterface
{
    public function match(RequestInterface $request): bool;
}
