<?php

namespace App\Controllers;

use App\Entities\Node;
use App\Managers\TreeManager;
use App\Repositories\TreeRepository;
use Lib\RequestInterface;
use Lib\Response;
use Lib\ResponseInterface;
use Lib\Router\RequestHandlerInterface;
use Lib\Utils\Reflection;

class CreateNodeAction implements RequestHandlerInterface
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
        $parentId = (int) $request->getQueryParams()['parent_id'];

        $tree = $this->treeRepository->getTree();

        $tree->appendChild(
            $parentId,
            Reflection::populatePublicFields(new Node(), [
                'name' => 'Node ' . substr(md5(random_bytes(100)), 0, 10),
            ])
        );

        $this->treeManager->save($tree);

        return Response::json([
            'success' => true,
        ]);
    }
}
