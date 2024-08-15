<?php

namespace Lib;

use Lib\Router\CallableRequestPredicate;
use Lib\Router\HandlerException;
use Lib\Router\RequestHandlerInterface;
use Lib\Router\RequestPredicateBuilder;
use Lib\Router\RequestPredicateInterface;
use Lib\Router\UnknownResourceException;
use Stringable;
use Throwable;

class Router
{
    /**
     * @var array<int, array{predicate: RequestPredicateInterface, handler: RequestHandlerInterface}>
     */
    private array $routes = [];

    public function tap(RequestHandlerInterface $handler): static
    {
        $this->routes[] = [
            'predicate' => new CallableRequestPredicate(fn () => true),
            'handler' => $handler,
        ];

        return $this;
    }

    public function get(string|RequestPredicateInterface $pathOrPredicate, RequestHandlerInterface $handler): static
    {
        $builder = $pathOrPredicate instanceof RequestPredicateInterface
            ? RequestPredicateBuilder::from($pathOrPredicate)
            : RequestPredicateBuilder::create()->withPath($pathOrPredicate);

        $predicate = $builder
            ->withHttpMethod('GET')
            ->build();

        $this->routes[] = [
            'predicate' => $predicate,
            'handler' => $handler,
        ];

        return $this;
    }

    public function post(string|RequestPredicateInterface $pathOrPredicate, RequestHandlerInterface $handler): static
    {
        $builder = $pathOrPredicate instanceof RequestPredicateInterface
            ? RequestPredicateBuilder::from($pathOrPredicate)
            : RequestPredicateBuilder::create()->withPath($pathOrPredicate);

        $predicate = $builder
            ->withHttpMethod('POST')
            ->build();

        $this->routes[] = [
            'predicate' => $predicate,
            'handler' => $handler,
        ];

        return $this;
    }

    public function postJson(string|RequestPredicateInterface $pathOrPredicate, RequestHandlerInterface $handler): static
    {
        $builder = $pathOrPredicate instanceof RequestPredicateInterface
            ? RequestPredicateBuilder::from($pathOrPredicate)
            : RequestPredicateBuilder::create()->withPath($pathOrPredicate);

        $predicate = $builder
            ->withHttpMethod('POST')
            ->withContentType('application/json')
            ->build();

        $this->routes[] = [
            'predicate' => $predicate,
            'handler' => $handler,
        ];

        return $this;
    }

    public function dispatch(Request $request): ResponseInterface
    {
        foreach ($this->routes as $item) {
            /** @var RequestHandlerInterface $handler */
            /** @var RequestPredicateInterface $predicate */
            ['predicate' => $predicate, 'handler' => $handler] = $item;

            if (true === $predicate->match($request)) {
                try {
                    return $handler->handle($request);
                } catch (Throwable $e) {
                    $handlerString = $handler instanceof Stringable
                        ? (string) $handler
                        : get_class($handler);
                    throw new HandlerException(sprintf('Handler failed: %s. Error: %s', $handlerString, $e->getMessage()), $e);
                }
            }
        }

        throw new UnknownResourceException();
    }
}
