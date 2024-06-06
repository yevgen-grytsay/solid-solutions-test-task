<?php

namespace Lib\Router;

use Lib\Request;

class CallableRequestPredicate implements RequestPredicateInterface
{
    private $predicate;

    public function __construct(callable $predicate)
    {
        $this->predicate = $predicate;
    }

    public function match(Request $request): bool
    {
        return call_user_func($this->predicate, $request);
    }
}
