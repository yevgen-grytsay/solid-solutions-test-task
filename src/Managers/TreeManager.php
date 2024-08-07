<?php

namespace App\Managers;

use App\Entities\Tree;
use App\Repositories\TreeRepository;
use Lib\Db\ConnectionInterface;

class TreeManager
{
    private ConnectionInterface $db;

    public function __construct(ConnectionInterface $db)
    {
        $this->db = $db;
    }

    public function save(Tree $tree): void
    {
        $this->db->transaction(function () use ($tree) {
            $this->saveTree($tree);
        });
    }

    private function saveTree(Tree $tree): void
    {
        $deletedIdList = $tree->popDeletedIds();
        foreach ($deletedIdList as $id) {
            $this->db->delete(TreeRepository::TABLE_NAME, $id);
        }

        $created = $tree->popCreated();
        foreach ($created as $node) {
            $id = $this->db->insert(TreeRepository::TABLE_NAME, [
                'name' => $node->name,
                'parent_id' => $node->parent_id,
            ]);
            $node->id = $id;
        }

        $updated = $tree->popUpdated();
        foreach ($updated as $node) {
            $this->db->update(TreeRepository::TABLE_NAME, $node['id'], $node['data']);
        }
    }
}
