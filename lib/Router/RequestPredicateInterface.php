<?php

namespace Lib\Router;

use Lib\Request;

interface RequestPredicateInterface
{
    public function match(Request $request): bool;
}
