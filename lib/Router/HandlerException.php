<?php

namespace Lib\Router;

use JetBrains\PhpStorm\Pure;
use Throwable;

class HandlerException extends RouterException
{
    public function __construct(string $message, Throwable $handlerError)
    {
        parent::__construct($message, 0, $handlerError);
    }
}
