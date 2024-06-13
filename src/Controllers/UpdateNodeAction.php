<?php

namespace App\Controllers;

use App\Managers\TreeManager;
use App\Repositories\TreeRepository;
use Lib\RequestInterface;
use Lib\Response;
use Lib\ResponseInterface;
use Lib\Router\RequestHandlerInterface;

class UpdateNodeAction implements RequestHandlerInterface
{
    private TreeRepository $treeRepository;
    private TreeManager $treeManager;

    public function __construct(
        TreeRepository $treeRepository,
        TreeManager $treeManager
    ) {
        $this->treeRepository = $treeRepository;
        $this->treeManager = $treeManager;
    }

    public function handle(RequestInterface $request): ResponseInterface
    {
        $data = json_decode($request->getContent(), true);

        $nodeId = (int) $request->getQueryParams()['id'];

        $tree = $this->treeRepository->getTree();

        $tree->updateNode($nodeId, $data);

        $this->treeManager->save($tree);

        return Response::json([
            'success' => true,
        ]);
    }
}
