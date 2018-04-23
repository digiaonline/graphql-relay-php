<?php

namespace Digia\GraphQL\Relay\Test\Unit;

use Digia\GraphQL\Relay\ArrayConnectionBuilder;
use Digia\GraphQL\Relay\ConnectionArguments;
use Digia\GraphQL\Relay\ConnectionInterface;
use Digia\GraphQL\Relay\Edge;
use Digia\GraphQL\Relay\RelayException;
use PHPUnit\Framework\TestCase;

class ArrayConnectionBuilderTest extends TestCase
{
    private const LETTERS = ['A', 'B', 'C', 'D', 'E'];

    // basic slicing

    public function testBasicSlicing()
    {
        $connection = $this->createConnection(self::LETTERS);

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
        $connection = $this->createConnection(self::LETTERS, ['first' => 2]);

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
        $connection = $this->createConnection(self::LETTERS, ['first' => 10]);

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
        $connection = $this->createConnection(self::LETTERS, ['last' => 2]);

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
        $connection = $this->createConnection(self::LETTERS, ['last' => 10]);

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
        $connection = $this->createConnection(self::LETTERS, [
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
        $connection = $this->createConnection(self::LETTERS, [
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
        $connection = $this->createConnection(self::LETTERS, [
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
        $connection = $this->createConnection(self::LETTERS, [
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
        $connection = $this->createConnection(self::LETTERS, [
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
        $connection = $this->createConnection(self::LETTERS, [
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
        $connection = $this->createConnection(self::LETTERS, [
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
        $connection = $this->createConnection(self::LETTERS, [
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
        $connection = $this->createConnection(self::LETTERS, [
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
        $connection = $this->createConnection(self::LETTERS, [
            'last'   => 3,
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

    // cursor edge cases

    // throws an error if first < 0

    public function testThrowsAnErrorIfFirstLessThanZero()
    {
        $this->expectException(RelayException::class);
        $this->expectExceptionMessage('Argument "first" must be a non-negative integer.');

        $this->createConnection(self::LETTERS, [
            'first' => -1,
        ]);
    }

    // throws an error if last < 0

    public function testThrowsAnErrorIfLastLessThanZero()
    {
        $this->expectException(RelayException::class);
        $this->expectExceptionMessage('Argument "last" must be a non-negative integer.');

        $this->createConnection(self::LETTERS, [
            'last' => -1,
        ]);
    }

    // returns all elements if cursors are invalid

    public function testReturnsAllElementsIfCursorsAreInvalid()
    {
        $connection = $this->createConnection(self::LETTERS, [
            'before' => 'invalid',
            'after'  => 'invalid',
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

    // returns all elements if cursors are on the outside

    public function testReturnsAllElementsIfCursorsAreOnTheOutside()
    {
        $connection = $this->createConnection(self::LETTERS, [
            'before' => 'YXJyYXljb25uZWN0aW9uOjYK',
            'after'  => 'YXJyYXljb25uZWN0aW9uOi0xCg==',
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

    // returns no elements if cursors cross

    public function testReturnsNoElementsIfCursorsCross()
    {
        $connection = $this->createConnection(self::LETTERS, [
            'before' => 'YXJyYXljb25uZWN0aW9uOjI=',
            'after'  => 'YXJyYXljb25uZWN0aW9uOjQ=',
        ]);

        $expected = [
            'edges'    => [
            ],
            'pageInfo' => [
                'startCursor'     => null,
                'endCursor'       => null,
                'hasPreviousPage' => false,
                'hasNextPage'     => false,
            ]
        ];

        $this->assertConnection($expected, $connection);
    }

    // TODO: cursorForObjectInConnection()

    // TODO: connectionFromPromisedArray()

    // connectionFromArraySlice()

    // works with a just-right array slice

    public function testWorksWithAJustRightArraySlice()
    {
        $connection = $this->createConnection(\array_slice(self::LETTERS, 1, 3), [
            'first' => 2,
            'after' => 'YXJyYXljb25uZWN0aW9uOjA=',
        ], 1, 5);

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

    // works with an oversized array slice ("left" side)

    public function testWorksWithAnOversizedArraySliceLeftSide()
    {
        $connection = $this->createConnection(\array_slice(self::LETTERS, 0, 4), [
            'first' => 2,
            'after' => 'YXJyYXljb25uZWN0aW9uOjA=',
        ], 0, 5);

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

    // works with an oversized array slice ("right" side)

    public function testWorksWithAnOversizedArraySliceRightSide()
    {
        $connection = $this->createConnection(\array_slice(self::LETTERS, 2, 2), [
            'first' => 1,
            'after' => 'YXJyYXljb25uZWN0aW9uOjE=',
        ], 2, 5);

        $expected = [
            'edges'    => [
                [
                    'node'   => 'C',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjI='
                ],
            ],
            'pageInfo' => [
                'startCursor'     => 'YXJyYXljb25uZWN0aW9uOjI=',
                'endCursor'       => 'YXJyYXljb25uZWN0aW9uOjI=',
                'hasPreviousPage' => false,
                'hasNextPage'     => true,
            ]
        ];

        $this->assertConnection($expected, $connection);
    }

    // works with an oversized array slice (both sides)

    public function testWorksWithAnOversizedArraySliceBothSide()
    {
        $connection = $this->createConnection(\array_slice(self::LETTERS, 1, 3), [
            'first' => 1,
            'after' => 'YXJyYXljb25uZWN0aW9uOjE=',
        ], 1, 5);

        $expected = [
            'edges'    => [
                [
                    'node'   => 'C',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjI='
                ],
            ],
            'pageInfo' => [
                'startCursor'     => 'YXJyYXljb25uZWN0aW9uOjI=',
                'endCursor'       => 'YXJyYXljb25uZWN0aW9uOjI=',
                'hasPreviousPage' => false,
                'hasNextPage'     => true,
            ]
        ];

        $this->assertConnection($expected, $connection);
    }

    // works with an undersized array slice ("left" side)

    public function testWorksWithAnUndersizedArraySliceLeftSide()
    {
        $connection = $this->createConnection(\array_slice(self::LETTERS, 3, 2), [
            'first' => 3,
            'after' => 'YXJyYXljb25uZWN0aW9uOjE=',
        ], 3, 5);

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
                'hasPreviousPage' => false,
                'hasNextPage'     => false,
            ]
        ];

        $this->assertConnection($expected, $connection);
    }

    // works with an undersized array slice ("right" side)

    public function testWorksWithAnUndersizedArraySliceRightSide()
    {
        $connection = $this->createConnection(\array_slice(self::LETTERS, 2, 2), [
            'first' => 3,
            'after' => 'YXJyYXljb25uZWN0aW9uOjE=',
        ], 2, 5);

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
                'hasPreviousPage' => false,
                'hasNextPage'     => true,
            ]
        ];

        $this->assertConnection($expected, $connection);
    }

    // works with an undersized array slice (both sides)

    public function testWorksWithAnUndersizedArraySliceBothSides()
    {
        $connection = $this->createConnection(\array_slice(self::LETTERS, 3, 1), [
            'first' => 3,
            'after' => 'YXJyYXljb25uZWN0aW9uOjE=',
        ], 3, 5);

        $expected = [
            'edges'    => [
                [
                    'node'   => 'D',
                    'cursor' => 'YXJyYXljb25uZWN0aW9uOjM='
                ],
            ],
            'pageInfo' => [
                'startCursor'     => 'YXJyYXljb25uZWN0aW9uOjM=',
                'endCursor'       => 'YXJyYXljb25uZWN0aW9uOjM=',
                'hasPreviousPage' => false,
                'hasNextPage'     => true,
            ]
        ];

        $this->assertConnection($expected, $connection);
    }

    // TODO: connectionFromPromisedArraySlice()

    /**
     * @param array $data
     * @param array $args
     * @return ConnectionInterface
     */
    private function createConnection(
        array $data,
        array $args = [],
        ?int $sliceStart = null,
        ?int $arrayLength = null
    ): ConnectionInterface {
        /** @noinspection PhpUnhandledExceptionInspection */
        return ArrayConnectionBuilder::fromArraySlice(
            $data,
            ConnectionArguments::fromArray($args),
            $sliceStart ?? 0,
            $arrayLength ?? \count($data)
        );
    }

    /**
     * @param array $expected
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
