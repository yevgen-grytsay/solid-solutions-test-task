<?php

namespace Lib\Router;

use JetBrains\PhpStorm\Pure;

class UnknownResourceException extends RouterException
{
    public function __construct(string $message = 'Unknown resource')
    {
        parent::__construct($message);
    }
}
