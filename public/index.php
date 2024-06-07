<?php

use Lib\ErrorRenderer;
use Lib\Request;
use Lib\Response;
use Lib\Router;
use Lib\Router\FunctionRequestHandler;

require_once __DIR__ . '/../vendor/autoload.php';

$router = (new Router())
    ->get('/get-all', FunctionRequestHandler::create(function (Request $request) {
        return Response::jsonError([
            'success' => true,
            'data' => [
                'tree' => [
                    'root' => [
                        'id' => 1,
                        'name' => 'Content Root method=' . $request->getMethod(),
                        'children' => [],
                    ]
                ],
            ],
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }))
    ->post('/create', FunctionRequestHandler::create(function (Request $request) {
        throw new \Lib\HttpException('Method "create" not implemented, path=' . $request->getPath(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }))
    ->post('/delete', FunctionRequestHandler::create(function (Request $request) {
        throw new \Lib\HttpException('Method "delete" not implemented, path=' . $request->getPath(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }))
;


$request = Request::createFromGlobals();
try {
    $response = $router->dispatch($request);
} catch(Throwable $e) {
    // var_dump($e);
    $response = (new ErrorRenderer())->render($e);
}

header(sprintf("HTTP/1.1 %d %s", $response->status, $response->getStatusText()));

foreach ($response->headers as $name => $value) {
    header($name . ': ' . $value);
}

echo $response->body;
