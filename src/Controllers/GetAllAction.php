<?php

namespace App\Controllers;

use App\Repositories\NodeRepository;
use Lib\ActionInterface;
use Lib\DefaultJsonRenderer;
use Lib\RequestInterface;
use Lib\Response;
use Lib\ResponseInterface;

class GetAllAction implements ActionInterface
{
    private NodeRepository $nodeRepository;

    public function __construct(NodeRepository $nodeRepository)
    {
        $this->nodeRepository = $nodeRepository;
    }

    public function index(RequestInterface $request): ResponseInterface
    {
        $tree = $this->nodeRepository->getTree();

        return new Response(
            (new DefaultJsonRenderer())->render([
                'success' => true,
                'data' => [
                    'tree' => [
                        'root' => $tree,
                    ],
                ],
            ]),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/json',
            ]
        );
    }
}
