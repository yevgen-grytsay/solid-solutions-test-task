<?php

namespace Entities;

use App\Entities\Node;
use App\Entities\Tree2;
use Lib\Utils\Reflection;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

class Tree2Test extends TestCase
{
    public function testJsonSerialize()
    {
        $nodes = [
            [
                'id' => '1',
                'name' => 'Root',
                'parent_id' => '0',
            ],
            [
                'id' => '2',
                'name' => 'Node #2',
                'parent_id' => '1',
            ],
            [
                'id' => '3',
                'name' => 'Node #3',
                'parent_id' => '1',
            ],
            [
                'id' => '4',
                'name' => 'Node #4',
                'parent_id' => '3',
            ],
            [
                'id' => '5',
                'name' => 'Node #5',
                'parent_id' => '1',
            ],
        ];

        $tree = new Tree2($nodes);

        $actual = $tree->jsonSerialize();

        $expected = Reflection::populatePublicFields(new Node(), [
            'id' => '1',
            'name' => 'Root',
            'parent_id' => '0',
            'children' => [
                Reflection::populatePublicFields(new Node(), [
                    'id' => '2',
                    'name' => 'Node #2',
                    'parent_id' => '1',
                    'children' => [],
                ]),
                Reflection::populatePublicFields(new Node(), [
                    'id' => '3',
                    'name' => 'Node #3',
                    'parent_id' => '1',
                    'children' => [
                        Reflection::populatePublicFields(new Node(), [
                            'id' => '4',
                            'name' => 'Node #4',
                            'parent_id' => '3',
                            'children' => [],
                        ]),
                    ],
                ]),
                Reflection::populatePublicFields(new Node(), [
                    'id' => '5',
                    'name' => 'Node #5',
                    'parent_id' => '1',
                    'children' => [],
                ]),
            ],
        ]);

        assertEquals(
            $actual['root'],
            $expected
        );
    }

    public function testDeleteNodeById()
    {
        $nodes = [
            [
                'id' => '1',
                'name' => 'Root',
                'parent_id' => '0',
            ],
            [
                'id' => '2',
                'name' => 'Node #2',
                'parent_id' => '1',
            ],
            [
                'id' => '3',
                'name' => 'Node #3',
                'parent_id' => '2',
            ],
            [
                'id' => '4',
                'name' => 'Node #4',
                'parent_id' => '2',
            ],
            [
                'id' => '5',
                'name' => 'Node #5',
                'parent_id' => '4',
            ],
        ];

        $tree = new Tree2($nodes);

        $tree->deleteNodeById(2);

        $actual = $tree->popDeleted();
        $expected = [
            Reflection::populatePublicFields(new Node(), [
                'id' => '2',
                'name' => 'Node #2',
                'parent_id' => '1',
            ]),
            Reflection::populatePublicFields(new Node(), [
                'id' => '3',
                'name' => 'Node #3',
                'parent_id' => '2',
            ]),
            Reflection::populatePublicFields(new Node(), [
                'id' => '4',
                'name' => 'Node #4',
                'parent_id' => '2',
            ]),
            Reflection::populatePublicFields(new Node(), [
                'id' => '5',
                'name' => 'Node #5',
                'parent_id' => '4',
            ]),
        ];
        assertEquals($expected, $actual);
    }
}
