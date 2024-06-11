<?php

namespace App\Controllers;

use App\Repositories\TreeRepository;
use Lib\ActionInterface;
use Lib\DefaultJsonRenderer;
use Lib\RequestInterface;
use Lib\Response;
use Lib\ResponseInterface;

class GetAllAction implements ActionInterface
{
    private TreeRepository $treeRepository;

    public function __construct(TreeRepository $treeRepository)
    {
        $this->treeRepository = $treeRepository;
    }

    public function index(RequestInterface $request): ResponseInterface
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
