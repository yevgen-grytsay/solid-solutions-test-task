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

    public function deleteById(string $table, $id, $idColumn = 'id'): array
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$table} WHERE {$idColumn} = :id");
        $result = $stmt->execute([
            ":{$idColumn}" => $id,
        ]);

        return [$result, $stmt->errorInfo()];
    }

    public function insert(string $table, array $row): false|string
    {
        $columnStr = implode(', ', array_keys($row));
        $valuesStr = implode(', ', array_fill(0, count($row), '?'));

        $stmt = $this->pdo->prepare("INSERT INTO {$table} ({$columnStr}) VALUES ($valuesStr)");

        $params = array_values($row);
        $stmt->execute($params);

        return $this->pdo->lastInsertId();
    }

    public function updateById(string $table, $id, $idColumn = 'id', array $data = []): void
    {
        unset($data[$idColumn]);

        $params = array_combine(
            array_map(fn(string $k) => ":{$k}", $data),
            $data,
        );

        $parts = array_map(fn(string $k) => "$k = :{$k}", array_keys($data));
        $partsStr = implode(', ', $parts);
        $stmt = $this->pdo->prepare("UPDATE {$table} SET {$partsStr} WHERE {$idColumn} = :id");
        $stmt->execute([
            ...$params,
            ':id' => $id,
        ]);
    }

    public function select(string $table, $where = null): false|array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$table}");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
