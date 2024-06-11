<?php

namespace Lib\Db;

use PDO;

class Connection
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getById(string $table, $id, $idColumn = 'id')
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE {$idColumn} = :id");
        $stmt->execute([
            ":{$idColumn}" => $id,
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function queryAll(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql); // TODO handle error/exception
        $stmt->execute($params); // TODO handle exception

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (false === $result) {
            throw new DbException('Can not fetch result');
        }

        return $result;
    }
}
