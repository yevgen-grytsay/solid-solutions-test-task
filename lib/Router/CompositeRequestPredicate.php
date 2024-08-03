<?php

namespace Lib\Router;

use Lib\RequestInterface;

class CompositeRequestPredicate implements RequestPredicateInterface
{
    /**
     * @var RequestPredicateInterface[]
     */
    private array $predicates = [];

    public static function from(array $predicateList): CompositeRequestPredicate
    {
        return new self(...$predicateList);
    }

    public function __construct(RequestPredicateInterface ...$predicates)
    {
        $this->predicates = $predicates;
    }

    public function match(RequestInterface $request): bool
    {
        foreach ($this->predicates as $p) {
            if (false === $p->match($request)) {
                return false;
            }
        }

        return true;
    }
}
