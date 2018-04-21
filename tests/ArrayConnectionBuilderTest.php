<?php

namespace Digia\GraphQL\Relay\Test;

use Digia\GraphQL\Relay\ArrayConnectionBuilder;
use Digia\GraphQL\Relay\ConnectionArguments;
use Digia\GraphQL\Relay\ConnectionInterface;
use Digia\GraphQL\Relay\Edge;
use PHPUnit\Framework\TestCase;

class ArrayConnectionBuilderTest extends TestCase
{

    // basic slicing

    public function testBasicSlicing()
    {
        $connection = $this->createConnection(['A', 'B', 'C', 'D', 'E']);

        $expected = [
            'edges'    => [
                [
                    'node'   => 'A',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjA='
                ],
                [
                    'node'   => 'B',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjE='
                ],
                [
                    'node'   => 'C',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjI='
                ],
                [
                    'node'   => 'D',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjM='
                ],
                [
                    'node'   => 'E',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjQ='
                ]
            ],
            'pageInfo' => [
                'startCursor'     => 'YXJyYXljb25uZWN0aW9uOjA=',
                'endCursor'       => 'YXJyYXljb25uZWN0aW9uOjQ=',
                'hasPreviousPage' => false,
                'hasNextPage'     => false,
            ]
        ];

        $this->assertConnection($expected, $connection);
    }

    // respects a smaller first

    public function testRespectsASmallerFirst()
    {
        $connection = $this->createConnection(['A', 'B', 'C', 'D', 'E'], ['first' => 2]);

        $expected = [
            'edges'    => [
                [
                    'node'   => 'A',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjA='
                ],
                [
                    'node'   => 'B',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjE='
                ]
            ],
            'pageInfo' => [
                'startCursor'     => 'YXJyYXljb25uZWN0aW9uOjA=',
                'endCursor'       => 'YXJyYXljb25uZWN0aW9uOjE=',
                'hasPreviousPage' => false,
                'hasNextPage'     => true,
            ]
        ];

        $this->assertConnection($expected, $connection);
    }

    // respects an overly large first

    public function testRespectsAnOverlyLargeFirst()
    {
        $connection = $this->createConnection(['A', 'B', 'C', 'D', 'E'], ['first' => 10]);

        $expected = [
            'edges'    => [
                [
                    'node'   => 'A',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjA='
                ],
                [
                    'node'   => 'B',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjE='
                ],
                [
                    'node'   => 'C',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjI='
                ],
                [
                    'node'   => 'D',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjM='
                ],
                [
                    'node'   => 'E',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjQ='
                ]
            ],
            'pageInfo' => [
                'startCursor'     => 'YXJyYXljb25uZWN0aW9uOjA=',
                'endCursor'       => 'YXJyYXljb25uZWN0aW9uOjQ=',
                'hasPreviousPage' => false,
                'hasNextPage'     => false,
            ]
        ];

        $this->assertConnection($expected, $connection);
    }

    // respects a smaller last

    public function testRespectsASmallerLast()
    {
        $connection = $this->createConnection(['A', 'B', 'C', 'D', 'E'], ['last' => 2]);

        $expected = [
            'edges'    => [
                [
                    'node'   => 'D',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjM='
                ],
                [
                    'node'   => 'E',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjQ='
                ]
            ],
            'pageInfo' => [
                'startCursor'     => 'YXJyYXljb25uZWN0aW9uOjM=',
                'endCursor'       => 'YXJyYXljb25uZWN0aW9uOjQ=',
                'hasPreviousPage' => true,
                'hasNextPage'     => false,
            ]
        ];

        $this->assertConnection($expected, $connection);
    }

    // respects an overly large last

    public function testRespectsAnOverlyLargeLast()
    {
        $connection = $this->createConnection(['A', 'B', 'C', 'D', 'E'], ['last' => 10]);

        $expected = [
            'edges'    => [
                [
                    'node'   => 'A',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjA='
                ],
                [
                    'node'   => 'B',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjE='
                ],
                [
                    'node'   => 'C',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjI='
                ],
                [
                    'node'   => 'D',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjM='
                ],
                [
                    'node'   => 'E',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjQ='
                ]
            ],
            'pageInfo' => [
                'startCursor'     => 'YXJyYXljb25uZWN0aW9uOjA=',
                'endCursor'       => 'YXJyYXljb25uZWN0aW9uOjQ=',
                'hasPreviousPage' => false,
                'hasNextPage'     => false,
            ]
        ];

        $this->assertConnection($expected, $connection);
    }

    // pagination

    // respects first and after

    public function testRespectsFirstAndAfter()
    {
        $connection = $this->createConnection(['A', 'B', 'C', 'D', 'E'], [
            'first' => 2,
            'after' => 'YXJyYXljb25uZWN0aW9uOjE=',
        ]);

        $expected = [
            'edges'    => [
                [
                    'node'   => 'C',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjI='
                ],
                [
                    'node'   => 'D',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjM='
                ]
            ],
            'pageInfo' => [
                'startCursor'     => 'YXJyYXljb25uZWN0aW9uOjI=',
                'endCursor'       => 'YXJyYXljb25uZWN0aW9uOjM=',
                'hasPreviousPage' => false,
                'hasNextPage'     => true,
            ]
        ];

        $this->assertConnection($expected, $connection);
    }

    // respects first and after with long first

    public function testRespectsFirstAndAfterWithOverlyLargeFirst()
    {
        $connection = $this->createConnection(['A', 'B', 'C', 'D', 'E'], [
            'first' => 10,
            'after' => 'YXJyYXljb25uZWN0aW9uOjE=',
        ]);

        $expected = [
            'edges'    => [
                [
                    'node'   => 'C',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjI='
                ],
                [
                    'node'   => 'D',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjM='
                ],
                [
                    'node'   => 'E',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjQ='
                ]
            ],
            'pageInfo' => [
                'startCursor'     => 'YXJyYXljb25uZWN0aW9uOjI=',
                'endCursor'       => 'YXJyYXljb25uZWN0aW9uOjQ=',
                'hasPreviousPage' => false,
                'hasNextPage'     => false,
            ]
        ];

        $this->assertConnection($expected, $connection);
    }

    // respects last and before

    public function testRespectsLastAndBefore()
    {
        $connection = $this->createConnection(['A', 'B', 'C', 'D', 'E'], [
            'last'   => 2,
            'before' => 'YXJyYXljb25uZWN0aW9uOjM=',
        ]);

        $expected = [
            'edges'    => [
                [
                    'node'   => 'B',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjE='
                ],
                [
                    'node'   => 'C',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjI='
                ],
            ],
            'pageInfo' => [
                'startCursor'     => 'YXJyYXljb25uZWN0aW9uOjE=',
                'endCursor'       => 'YXJyYXljb25uZWN0aW9uOjI=',
                'hasPreviousPage' => true,
                'hasNextPage'     => false,
            ]
        ];

        $this->assertConnection($expected, $connection);
    }

    // respects last and before with long last

    public function testRespectsLastAndBeforeWithOverlyLargeLast()
    {
        $connection = $this->createConnection(['A', 'B', 'C', 'D', 'E'], [
            'last'   => 10,
            'before' => 'YXJyYXljb25uZWN0aW9uOjM=',
        ]);

        $expected = [
            'edges'    => [
                [
                    'node'   => 'A',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjA='
                ],
                [
                    'node'   => 'B',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjE='
                ],
                [
                    'node'   => 'C',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjI='
                ],
            ],
            'pageInfo' => [
                'startCursor'     => 'YXJyYXljb25uZWN0aW9uOjA=',
                'endCursor'       => 'YXJyYXljb25uZWN0aW9uOjI=',
                'hasPreviousPage' => false,
                'hasNextPage'     => false,
            ]
        ];

        $this->assertConnection($expected, $connection);
    }

    // respects first and after and before, too few

    public function testRespectsFirstAndAfterAndBeforeTooFew()
    {
        $connection = $this->createConnection(['A', 'B', 'C', 'D', 'E'], [
            'first'  => 2,
            'after'  => 'YXJyYXljb25uZWN0aW9uOjA=',
            'before' => 'YXJyYXljb25uZWN0aW9uOjQ=',
        ]);

        $expected = [
            'edges'    => [
                [
                    'node'   => 'B',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjE='
                ],
                [
                    'node'   => 'C',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjI='
                ],
            ],
            'pageInfo' => [
                'startCursor'     => 'YXJyYXljb25uZWN0aW9uOjE=',
                'endCursor'       => 'YXJyYXljb25uZWN0aW9uOjI=',
                'hasPreviousPage' => false,
                'hasNextPage'     => true,
            ]
        ];

        $this->assertConnection($expected, $connection);
    }

    // respects first and after and before, too many

    public function testRespectsFirstAndAfterAndBeforeTooMany()
    {
        $connection = $this->createConnection(['A', 'B', 'C', 'D', 'E'], [
            'first'  => 4,
            'after'  => 'YXJyYXljb25uZWN0aW9uOjA=',
            'before' => 'YXJyYXljb25uZWN0aW9uOjQ=',
        ]);

        $expected = [
            'edges'    => [
                [
                    'node'   => 'B',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjE='
                ],
                [
                    'node'   => 'C',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjI='
                ],
                [
                    'node'   => 'D',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjM='
                ],
            ],
            'pageInfo' => [
                'startCursor'     => 'YXJyYXljb25uZWN0aW9uOjE=',
                'endCursor'       => 'YXJyYXljb25uZWN0aW9uOjM=',
                'hasPreviousPage' => false,
                'hasNextPage'     => false,
            ]
        ];

        $this->assertConnection($expected, $connection);
    }

    // respects first and after and before, exactly right

    public function testRespectsFirstAndAfterAndBeforeExactlyRight()
    {
        $connection = $this->createConnection(['A', 'B', 'C', 'D', 'E'], [
            'first'  => 3,
            'after'  => 'YXJyYXljb25uZWN0aW9uOjA=',
            'before' => 'YXJyYXljb25uZWN0aW9uOjQ=',
        ]);

        $expected = [
            'edges'    => [
                [
                    'node'   => 'B',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjE='
                ],
                [
                    'node'   => 'C',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjI='
                ],
                [
                    'node'   => 'D',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjM='
                ],
            ],
            'pageInfo' => [
                'startCursor'     => 'YXJyYXljb25uZWN0aW9uOjE=',
                'endCursor'       => 'YXJyYXljb25uZWN0aW9uOjM=',
                'hasPreviousPage' => false,
                'hasNextPage'     => false,
            ]
        ];

        $this->assertConnection($expected, $connection);
    }

    // respects last and after and before, too few

    public function testRespectsLastAndAfterAndBeforeTooFew()
    {
        $connection = $this->createConnection(['A', 'B', 'C', 'D', 'E'], [
            'last'   => 2,
            'after'  => 'YXJyYXljb25uZWN0aW9uOjA=',
            'before' => 'YXJyYXljb25uZWN0aW9uOjQ=',
        ]);

        $expected = [
            'edges'    => [
                [
                    'node'   => 'C',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjI='
                ],
                [
                    'node'   => 'D',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjM='
                ],
            ],
            'pageInfo' => [
                'startCursor'     => 'YXJyYXljb25uZWN0aW9uOjI=',
                'endCursor'       => 'YXJyYXljb25uZWN0aW9uOjM=',
                'hasPreviousPage' => true,
                'hasNextPage'     => false,
            ]
        ];

        $this->assertConnection($expected, $connection);
    }

    // respects last and after and before, too many

    public function testRespectsLastAndAfterAndBeforeTooMany()
    {
        $connection = $this->createConnection(['A', 'B', 'C', 'D', 'E'], [
            'last'   => 4,
            'after'  => 'YXJyYXljb25uZWN0aW9uOjA=',
            'before' => 'YXJyYXljb25uZWN0aW9uOjQ=',
        ]);

        $expected = [
            'edges'    => [
                [
                    'node'   => 'B',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjE='
                ],
                [
                    'node'   => 'C',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjI='
                ],
                [
                    'node'   => 'D',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjM='
                ],
            ],
            'pageInfo' => [
                'startCursor'     => 'YXJyYXljb25uZWN0aW9uOjE=',
                'endCursor'       => 'YXJyYXljb25uZWN0aW9uOjM=',
                'hasPreviousPage' => false,
                'hasNextPage'     => false,
            ]
        ];

        $this->assertConnection($expected, $connection);
    }

    // respects last and after and before, exactly right

    public function testRespectsLastAndAfterAndBeforeExactlyRight()
    {
        $connection = $this->createConnection(['A', 'B', 'C', 'D', 'E'], [
            'last' => 3,
            'after'  => 'YXJyYXljb25uZWN0aW9uOjA=',
            'before' => 'YXJyYXljb25uZWN0aW9uOjQ=',
        ]);

        $expected = [
            'edges'    => [
                [
                    'node'   => 'B',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjE='
                ],
                [
                    'node'   => 'C',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjI='
                ],
                [
                    'node'   => 'D',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjM='
                ],
            ],
            'pageInfo' => [
                'startCursor'     => 'YXJyYXljb25uZWN0aW9uOjE=',
                'endCursor'       => 'YXJyYXljb25uZWN0aW9uOjM=',
                'hasPreviousPage' => false,
                'hasNextPage'     => false,
            ]
        ];

        $this->assertConnection($expected, $connection);
    }

    /**
     * @param array $data
     * @param array $args
     * @return ConnectionInterface
     */
    private function createConnection(array $data, array $args = []): ConnectionInterface
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return ArrayConnectionBuilder::fromArray(
            $data,
            ConnectionArguments::fromArray($args)
        );
    }

    /**
     * @param array               $expected
     * @param ConnectionInterface $connection
     */
    private function assertConnection(array $expected, ConnectionInterface $connection)
    {
        $edges    = $connection->getEdges();
        $pageInfo = $connection->getPageInfo();

        $this->assertEquals($expected, [
            'edges'    => \array_map(function (Edge $edge) {
                return ['node' => $edge->getNode(), 'cursor' => $edge->getCursor()];
            }, $edges),
            'pageInfo' => [
                'startCursor'     => $pageInfo->getStartCursor(),
                'endCursor'       => $pageInfo->getEndCursor(),
                'hasPreviousPage' => $pageInfo->hasPreviousPage(),
                'hasNextPage'     => $pageInfo->hasNextPage(),
            ]
        ]);
    }
}
