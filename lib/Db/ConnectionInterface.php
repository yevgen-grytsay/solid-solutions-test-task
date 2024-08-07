<?php

namespace Lib\Db;

interface ConnectionInterface
{
    public const REPEATABLE_READ = 'REPEATABLE READ';

    public function all(string $table): array;

    public function get(string $table, int $id): ?array;

    public function insert(string $table, array $data): int;

    public function update(string $table, int $id, array $data): void;

    public function delete(string $table, int $id): void;

    public function createQuery(): QueryInterface;

    public function transaction(callable $callback, $isolationLevel = null): mixed;
}
