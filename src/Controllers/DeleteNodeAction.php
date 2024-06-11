<?php

namespace App\Controllers;

use App\Managers\TreeManager;
use App\Repositories\TreeRepository;
use Lib\ActionInterface;
use Lib\RequestInterface;
use Lib\Response;
use Lib\ResponseInterface;

class DeleteNodeAction implements ActionInterface
{
    private TreeRepository $treeRepository;
    private TreeManager $treeManager;

    public function __construct(TreeRepository $treeRepository, TreeManager $treeManager)
    {
        $this->treeRepository = $treeRepository;
        $this->treeManager = $treeManager;
    }

    public function index(RequestInterface $request): ResponseInterface
    {
        $nodeId = (int) $request->getQueryParams()['id'];

        $tree = $this->treeRepository->getTree();

        $tree->deleteNodeById($nodeId);

        $this->treeManager->save($tree);

        return Response::json([
            'success' => true,
        ]);
    }
}
