<?php

namespace App\Repositories;

use App\Entities\Tree2;
use Lib\Db\Connection;

class TreeRepository
{
    public const TABLE_NAME = 'tree_2';

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getTree(): Tree2
    {
        $rows = $this->connection->select(self::TABLE_NAME);

        return new Tree2($rows);
    }
}
