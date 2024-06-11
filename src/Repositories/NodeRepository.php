<?php

namespace App\Repositories;

use App\Entities\Node;
use Lib\Db\Connection;

class NodeRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getTree(): Node
    {
        $row = $this->connection->getById('tree_1', 1);

        return Node::createFromJson($row['json']);
    }
}
