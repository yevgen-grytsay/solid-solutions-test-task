<?php

namespace Lib\Router;

use Lib\Request;

class CallableRequestPredicate implements RequestPredicateInterface
{
    /**
     * @var callable
     */
    private $predicate;

    public function __construct(callable $predicate)
    {
        $this->predicate = $predicate;
    }

    /** @psalm-suppress MixedInferredReturnType */
    public function match(Request $request): bool
    {
        /** @psalm-suppress MixedReturnStatement */
        return call_user_func($this->predicate, $request);
    }
}
