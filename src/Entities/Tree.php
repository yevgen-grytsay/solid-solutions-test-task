<?php

namespace App\Entities;

use JsonSerializable;
use Lib\Utils\Arr;
use Lib\Utils\Reflection;

class Tree implements JsonSerializable
{
    private array $created = [];
    private array $deleted = [];
    private array $updated = [];

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
     * @return int[]
     */
    public function popDeletedIds(): array
    {
        $deleted = $this->deleted;
        $this->deleted = [];

        return $deleted;
    }

    /**
     * @return Node[]
     */
    public function popUpdated(): array
    {
        $updated = $this->updated;
        $this->updated = [];

        return $updated;
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

    public function deleteNodeById(int $nodeId): void
    {
        $queue = [
            $this->getProtoNodeById($nodeId)
        ];
        $parentIndex = Arr::groupBy($this->nodeIndex, 'parent_id');

        while ($queue !== []) {
            $current = array_shift($queue);
            $this->deleted[] = $current['id'];

            $children = $parentIndex[$current['id']] ?? [];
            $queue = [
                ...$queue,
                ...$children,
            ];
        }
    }

    public function updateNode(int $id, array $data): void
    {
        // $node = Reflection::populatePublicFields(new Node(), $this->getProtoNodeById($id));

        $data = array_intersect_key($data, array_flip(['name']));

        $this->updated[] = [
            'id' => $id,
            'data' => $data,
        ];
    }
}
