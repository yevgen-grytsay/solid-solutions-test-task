<?php

namespace App\Controllers;

use App\Entities\Node;
use App\Managers\TreeManager;
use App\Repositories\TreeRepository;
use Lib\ActionInterface;
use Lib\RequestInterface;
use Lib\Response;
use Lib\ResponseInterface;
use Lib\Utils\Reflection;

class CreateNodeAction implements ActionInterface
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
        $parentId = (int) $request->getQueryParams()['parent_id'];

        $tree = $this->treeRepository->getTree();

        $tree->appendChild(
            $parentId,
            Reflection::populatePublicFields(new Node(), ['name' => 'Node ' . md5(random_bytes(100))])
        );

        $this->treeManager->save($tree);

        return Response::json([
            'success' => true,
        ]);
    }
}
