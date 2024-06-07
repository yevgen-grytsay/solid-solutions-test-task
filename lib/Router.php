<?php

namespace Lib;

use Lib\Router\RequestHandlerInterface;
use Lib\Router\RequestPredicateBuilder;
use Lib\Router\RequestPredicateInterface;
use Lib\Router\RouterException;
use Lib\Router\UnknownResourceException;
use Throwable;

class Router
{
    /**
     * @var array<int, array{predicate: RequestPredicateInterface, handler: RequestHandlerInterface}>
     */
    private array $routes = [];

    public function get(string|RequestPredicateInterface $pathOrPredicate, RequestHandlerInterface $handler): static
    {
        $predicate = $pathOrPredicate instanceof RequestPredicateInterface
            ? RequestPredicateBuilder::from($pathOrPredicate)
            : RequestPredicateBuilder::create()->withPath($pathOrPredicate);

        $predicate = $predicate->withHttpMethod('GET');

        $this->routes[] = [
            'predicate' => $predicate,
            'handler' => $handler,
        ];

        return $this;
    }

    public function post(string|RequestPredicateInterface $pathOrPredicate, RequestHandlerInterface $handler): static
    {
        $predicate = $pathOrPredicate instanceof RequestPredicateInterface
            ? RequestPredicateBuilder::from($pathOrPredicate)
            : RequestPredicateBuilder::create()->withPath($pathOrPredicate);

        $predicate = $predicate->withHttpMethod('POST');

        $this->routes[] = [
            'predicate' => $predicate,
            'handler' => $handler,
        ];

        return $this;
    }

    public function dispatch(Request $request): Response
    {
        foreach ($this->routes as $item) {
            /** @var RequestHandlerInterface $handler */
            /** @var RequestPredicateInterface $predicate */
            ['predicate' => $predicate, 'handler' => $handler] = $item;

            if (true === $predicate->match($request)) {
                try {
                    return $handler->handle($request);
                } catch (Throwable $e) {
                    throw new RouterException($e);
                }
            }
        }

        throw new UnknownResourceException();
    }
}
