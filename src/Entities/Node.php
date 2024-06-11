<?php

namespace App\Entities;

class Node
{
    public int $id = 0;
    public string $name = '';
    /**
     * @var Node[]
     */
    public array $children = [];
    public int $parent_id;
}
