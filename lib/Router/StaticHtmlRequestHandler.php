<?php

namespace Lib\Router;

use Lib\RequestInterface;
use Lib\Response;
use Lib\ResponseInterface;

class StaticHtmlRequestHandler implements RequestHandlerInterface
{
    private string $pathToFile;

    public function __construct(string $pathToFile)
    {
        $this->pathToFile = $pathToFile;
    }

    public function __toString(): string
    {
        return __CLASS__;
    }

    public function handle(RequestInterface $request): ResponseInterface
    {
        return Response::html(file_get_contents($this->pathToFile));
    }
}
