<?php

namespace Lib\Db;

use PDO;
use RuntimeException;
use Throwable;

class MySqlConnection implements ConnectionInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function all(string $table): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$table}");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function get(string $table, int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE id = :id");
        $stmt->execute([
            ":id" => $id,
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function insert(string $table, array $data): int
    {
        unset($data['id']);

        $columnStr = implode(', ', array_keys($data));
        $valuesStr = implode(', ', array_fill(0, count($data), '?'));

        $stmt = $this->pdo->prepare("INSERT INTO {$table} ({$columnStr}) VALUES ($valuesStr)");
        $stmt->execute(array_values($data));

        return $this->pdo->lastInsertId();
    }

    public function update(string $table, int $id, array $data): void
    {
        unset($data['id']);

        $params = array_combine(
            array_map(fn (string $k) => ":{$k}", array_keys($data)),
            $data,
        );

        $parts = array_map(fn (string $k) => "`$k` = :{$k}", array_keys($data));
        $partsStr = implode(', ', $parts);
        $stmt = $this->pdo->prepare("UPDATE {$table} SET {$partsStr} WHERE id = :id");
        $stmt->execute([
            ...$params,
            ':id' => $id,
        ]);
    }

    public function delete(string $table, int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$table} WHERE id = :id");
        $stmt->execute([
            ":id" => $id,
        ]);
    }

    public function createQuery(): QueryInterface
    {
        throw new RuntimeException('Not implemented: ' . __METHOD__);
    }

    /**
     * @throws Throwable
     */
    public function transaction(callable $callback, $isolationLevel = ConnectionInterface::REPEATABLE_READ): mixed
    {
        $this->pdo->query("SET TRANSACTION ISOLATION LEVEL {$isolationLevel}");
        $this->pdo->beginTransaction(); // todo check return value

        try {
            $result = call_user_func($callback, $this);
            $this->pdo->commit(); // todo check return value
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack(); // todo check return value
            }

            throw $e;
        }

        return $result;
    }
}
