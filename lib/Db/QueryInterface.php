<?php

namespace Lib\Db;

interface QueryInterface
{
    public function execute(string $sql): mixed;
}
