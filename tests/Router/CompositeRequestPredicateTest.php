<?php

namespace Router;

use Lib\RequestInterface;
use Lib\Router\CompositeRequestPredicate;
use Lib\Router\RequestPredicateInterface;
use PHPUnit\Framework\TestCase;

class CompositeRequestPredicateTest extends TestCase
{
    public function testMatch()
    {
        $predicate = new CompositeRequestPredicate(
            $this->createConfiguredStub(
                RequestPredicateInterface::class,
                ['match' => true,]
            )
        );

        self::assertTrue($predicate->match(self::createStub(RequestInterface::class)));
    }

    public function testMatchTwo()
    {
        $predicate = new CompositeRequestPredicate(
            $this->createConfiguredStub(
                RequestPredicateInterface::class,
                ['match' => true,]
            ),
            $this->createConfiguredStub(
                RequestPredicateInterface::class,
                ['match' => true,]
            )
        );

        self::assertTrue($predicate->match(self::createStub(RequestInterface::class)));
    }

    public function testDoesNotMatch()
    {
        $predicate = new CompositeRequestPredicate(
            $this->createConfiguredStub(
                RequestPredicateInterface::class,
                ['match' => false,]
            )
        );

        self::assertFalse($predicate->match(self::createStub(RequestInterface::class)));
    }

    public function testDoesNotMatchMultiplePredicates()
    {
        $predicate = new CompositeRequestPredicate(
            $this->createConfiguredStub(
                RequestPredicateInterface::class,
                ['match' => true,]
            ),
            $this->createConfiguredStub(
                RequestPredicateInterface::class,
                ['match' => false,]
            )
        );

        self::assertFalse($predicate->match(self::createStub(RequestInterface::class)));
    }
}
