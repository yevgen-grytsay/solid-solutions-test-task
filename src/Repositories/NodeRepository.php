<?php

namespace App\Repositories;

use App\Entities\Node;
use App\Entities\Tree;
use Lib\Db\Connection;

class NodeRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getTree(): Tree
    {
        $row = $this->connection->getById('tree_1', 1);

        $data = json_decode($row['json'], true);

        return new Tree($data['auto_increment'], Node::createFromArray($data['root']));
    }
}
