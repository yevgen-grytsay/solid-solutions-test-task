<?php

namespace App\Managers;

use App\Entities\Tree2;
use App\Repositories\TreeRepository;
use Lib\Db\Connection;

class TreeManager
{
    private Connection $db;

    /**
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function save(Tree2 $tree): void
    {
        $deleted = $tree->popDeleted();
        foreach ($deleted as $node) {
            $this->db->deleteById(TreeRepository::TABLE_NAME, $node->id);
        }

        $created = $tree->popCreated();
        foreach ($created as $node) {
            $id = (int) $this->db->insert(TreeRepository::TABLE_NAME, [
                'name' => $node->name,
                'parent_id' => $node->parent_id,
            ]);
            $node->id = $id;
        }
    }
}
