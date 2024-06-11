<?php

namespace App\Repositories;

use App\Entities\Tree;
use Lib\Db\ConnectionInterface;

class TreeRepository
{
    public const TABLE_NAME = 'tree';

    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function getTree(): Tree
    {
        $rows = $this->connection->all(self::TABLE_NAME);

        return new Tree($rows);
    }
}
