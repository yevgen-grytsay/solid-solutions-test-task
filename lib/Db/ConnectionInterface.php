<?php

namespace Lib\Db;

interface ConnectionInterface
{
    public function all(string $table): array;

    public function get(string $table, int $id): ?array;

    public function insert(string $table, array $data): int;

    public function update(string $table, int $id, array $data);

    public function delete(string $table, int $id): void;

    public function createQuery(): QueryInterface;
}
