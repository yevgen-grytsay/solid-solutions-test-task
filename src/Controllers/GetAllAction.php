<?php

namespace App\Controllers;

use App\Repositories\TreeRepository;
use Lib\DefaultJsonRenderer;
use Lib\RequestInterface;
use Lib\Response;
use Lib\ResponseInterface;
use Lib\Router\RequestHandlerInterface;

class GetAllAction implements RequestHandlerInterface
{
    private TreeRepository $treeRepository;

    public function __construct(TreeRepository $treeRepository)
    {
        $this->treeRepository = $treeRepository;
    }

    public function handle(RequestInterface $request): ResponseInterface
    {
        $tree = $this->treeRepository->getTree();

        return new Response(
            (new DefaultJsonRenderer())->render([
                'success' => true,
                'data' => [
                    'tree' => $tree,
                ],
            ]),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/json',
            ]
        );
    }
}
