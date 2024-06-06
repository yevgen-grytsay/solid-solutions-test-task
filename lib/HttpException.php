<?php

namespace Lib;

use JetBrains\PhpStorm\Pure;
use RuntimeException;

class HttpException extends RuntimeException
{
    public readonly int $httpCode;

    #[Pure] public function __construct(string $message = "", int $httpCode = 0)
    {
        parent::__construct($message);
        $this->httpCode = $httpCode;
    }
}
