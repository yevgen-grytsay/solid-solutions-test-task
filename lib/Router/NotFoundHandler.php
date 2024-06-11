<?php

namespace Lib\Router;

use Lib\RequestInterface;
use Lib\Response;
use Lib\ResponseInterface;

class NotFoundHandler implements RequestHandlerInterface
{
    public function __toString(): string
    {
        return __CLASS__;
    }

    public function handle(RequestInterface $request): ResponseInterface
    {
        if ($request->isJson()) {
            return Response::json([
                'success' => false,
                'error' => [
                    'message' => 'Resource not found',
                ]
            ]);
        }

        return Response::html(
            '<h1>Resource not found</h1>',
            Response::HTTP_NOT_FOUND,
        );
    }
}
