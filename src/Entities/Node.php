<?php

namespace App\Entities;

use Lib\Utils\Reflection;

class Node
{
    public int $id = 0;
    public string $name = '';
    /**
     * @var Node[]
     */
    public array $children = [];

    public static function createFromJson(string $json): Node
    {
        $root = json_decode($json, true);

        $nodes = self::createChildren([$root]);

        return $nodes[0];
    }

    private static function createChildren(array $children): array
    {
        $result = [];
        foreach ($children as $child) {
            $node = new Node();
            $node = Reflection::populatePublicFields(
                $node,
                array_diff_key($child, array_flip(['children']))
            );
            $node->children = self::createChildren($child['children'] ?? []);
            $result[] = $node;
        }

        return $result;
    }
}
