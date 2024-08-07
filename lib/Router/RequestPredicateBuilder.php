<?php

namespace Lib\Router;

use Lib\Request;

class RequestPredicateBuilder
{
    /**
     * @var RequestPredicateInterface[]
     */
    private array $predicates = [];

    public static function from(RequestPredicateInterface $predicate): self
    {
        $obj = new self();
        $obj->predicates[] = $predicate;

        return $obj;
    }

    public static function create(): self
    {
        return new self();
    }

    /** @psalm-api */
    public function withPredicate(RequestPredicateInterface $predicate): self
    {
        $predicates = $this->predicates;
        $predicates[] = $predicate;

        $obj = clone $this;
        $obj->predicates = $predicates;

        return $obj;
    }

    public function withPath(string $path): self
    {
        $predicates = $this->predicates;
        $predicates[] = new CallableRequestPredicate(function (Request $request) use ($path) {
            return $path === $request->getPath();
        });

        $obj = clone $this;
        $obj->predicates = $predicates;

        return $obj;
    }

    public function withHttpMethod(string $method): static
    {
        $predicates = $this->predicates;
        $predicates[] = new CallableRequestPredicate(function (Request $request) use ($method) {
            return strtolower($method) === strtolower($request->getMethod());
        });

        $obj = clone $this;
        $obj->predicates = $predicates;

        return $obj;
    }

    public function withContentType(string $contentType): static
    {
        $predicates = $this->predicates;
        $predicates[] = new CallableRequestPredicate(function (Request $request) use ($contentType) {
            return $contentType === $request->getContentType();
        });

        $obj = clone $this;
        $obj->predicates = $predicates;

        return $obj;
    }

    public function build(): RequestPredicateInterface
    {
        return CompositeRequestPredicate::from($this->predicates);
    }
}
