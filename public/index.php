<?php

use App\Controllers\CreateNodeAction;
use App\Controllers\DeleteNodeAction;
use App\Controllers\GetAllAction;
use App\Controllers\UpdateNodeAction;
use App\Managers\TreeManager;
use App\Repositories\TreeRepository;
use Lib\Db\MySqlConnection;
use Lib\ErrorRenderer;
use Lib\Request;
use Lib\Router;
use Lib\Router\NotFoundRequestHandler;
use Lib\Router\StaticHtmlRequestHandler;

require_once __DIR__ . '/../vendor/autoload.php';

$db = new MySqlConnection((function (): PDO {
    $pdo = new PDO("mysql:host=mysql;dbname=app;charset=utf8mb4", 'uapp', 'uapp123');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $pdo;
})());

$router = (new Router())
    ->get('/', new StaticHtmlRequestHandler(__DIR__ . '/../src/views/index.html'))
    ->get(
        '/get-all',
        new GetAllAction(
            new TreeRepository($db),
        )
    )
    ->post(
        '/create',
        new CreateNodeAction(
            new TreeRepository($db),
            new TreeManager($db)
        )
    )
    ->post(
        '/delete',
        new DeleteNodeAction(
            new TreeRepository($db),
            new TreeManager($db)
        )
    )
    ->postJson(
        '/update',
        new UpdateNodeAction(
            new TreeRepository($db),
            new TreeManager($db)
        )
    )
    ->tap(new NotFoundRequestHandler())
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
