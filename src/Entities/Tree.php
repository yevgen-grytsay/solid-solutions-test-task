<?php

namespace App\Entities;

class Tree
{
    private int $autoIncrement;
    private Node $root;

    public function __construct(int $autoIncrement, Node $root)
    {
        $this->autoIncrement = $autoIncrement;
        $this->root = $root;
    }

    public function getRoot(): Node
    {
        return $this->root;
    }
}
