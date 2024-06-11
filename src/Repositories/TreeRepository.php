<?php

namespace App\Repositories;

use App\Entities\Tree;
use Lib\Db\Connection;

class TreeRepository
{
    public const TABLE_NAME = 'tree';

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getTree(): Tree
    {
        $rows = $this->connection->select(self::TABLE_NAME);

        return new Tree($rows);
    }
}
