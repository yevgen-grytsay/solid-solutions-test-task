<?php

namespace Lib;

use Throwable;

class ErrorRenderer
{
    public function render(Throwable $e): Response
    {

        if ($e instanceof Router\UnknownResourceException) {
            return new Response("Resource not found", Response::HTTP_NOT_FOUND);
        }

        if ($e instanceof HttpException) {
            return Response::jsonError([
                'success' => false,
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ], $e->httpCode);
        }

        return Response::jsonError([
            'success' => false,
            'error' => [
                'message' => $e->getMessage(),
            ],
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
