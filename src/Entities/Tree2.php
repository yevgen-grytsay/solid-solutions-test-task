<?php

namespace App\Entities;

use JsonSerializable;
use Lib\Utils\Arr;
use Lib\Utils\Reflection;

class Tree2 implements JsonSerializable
{
    private array $created = [];
    private array $deleted = [];

    private array $nodeIndex;

    public function __construct(array $nodeList)
    {
        $this->nodeIndex = Arr::indexBy($nodeList, 'id');
    }

    public function appendChild(int $parentId, Node $nodeProto): static
    {
        $parent = $this->getProtoNodeById($parentId);

        $newNode = clone $nodeProto;
        $newNode->id = 0;
        $newNode->children = [];
        $newNode->parent_id = $parent['id'];

        $this->created[] = $newNode;

        return $this;
    }

    private function getProtoNodeById(int $id): array
    {
        return $this->nodeIndex[$id];
    }

    /**
     * @return Node[]
     */
    public function popCreated(): array
    {
        $created = $this->created;
        $this->created = [];

        return $created;
    }

    /**
     * @return Node[]
     */
    public function popDeleted(): array
    {
        $deleted = $this->deleted;
        $this->deleted = [];

        return $deleted;
    }

    public function jsonSerialize(): array
    {
        $root = Arr::findOne($this->nodeIndex, function (array $node) {
            $isOk = ((int)$node['parent_id']) === 0;

            return $isOk;
        });

        $rootNode = Reflection::populatePublicFields(new Node(), $root);

        $parentIndex = Arr::groupBy($this->nodeIndex, 'parent_id');

        $queue = [$rootNode];
        while ($queue !== []) {
            $current = array_shift($queue);

            $nodesProto = $parentIndex[$current->id] ?? [];
            $childNodes = array_map(function (array $row) {
                $node = new Node();
                $node = Reflection::populatePublicFields($node, $row);

                return $node;
            }, $nodesProto);
            $current->children = $childNodes;
            $queue = [
                ...$queue,
                ...$childNodes,
            ];
        }

        return [
            'root' => $rootNode,
        ];
    }
}
