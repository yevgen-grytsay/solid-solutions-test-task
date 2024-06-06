<?php

use Lib\ErrorRenderer;
use Lib\Request;
use Lib\Response;
use Lib\Router;
use Lib\Router\FunctionRequestHandler;

require_once __DIR__ . '/../vendor/autoload.php';

$router = (new Router())
    ->get('/get-all', FunctionRequestHandler::create(function (Request $request) {
        return new Response(sprintf("Hello, \"%s\"", $request->getPath()));
    }))
    ->post('/create', FunctionRequestHandler::create(function (Request $request) {
        throw new \Lib\HttpException('Method "create" not implemented', Response::HTTP_INTERNAL_SERVER_ERROR);
    }))
    ->post('/delete', FunctionRequestHandler::create(function (Request $request) {
        throw new \Lib\HttpException('Method "delete" not implemented', Response::HTTP_INTERNAL_SERVER_ERROR);
    }))
;


$request = Request::createFromGlobals();
try {
    $response = $router->dispatch($request);
} catch(Throwable $e) {
    // var_dump($e);
    $response = (new ErrorRenderer())->render($e);
}

foreach ($response->headers as $name => $value) {
    header($name . ': ' . $value);
}

echo $response->body;
