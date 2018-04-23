<?php

namespace Digia\GraphQL\Relay\Test\Functional;

use Digia\GraphQL\Schema\Schema;
use PHPUnit\Framework\TestCase;
use function Digia\GraphQL\graphql;

class StarWarsConnectionTest extends TestCase
{

    /**
     * @var Schema[]
     */
    protected $schemas;

    public function setUp()
    {
        $this->schemas = [
            starWarsSchemaWithArrayConnection(),
            starWarsSchemaWithStoreConnection()
        ];
    }

    // Star Wars connections

    // fetches the first ship of the rebels

    public function testFetchesTheFirstShipOfTheRebels()
    {
        $query = '
        query RebelsShipsQuery {
          rebels {
            name,
            ships(first: 1) {
              edges {
                node {
                  name
                }
              }
            }
          }
        }
        ';

        $expected = [
            'rebels' => [
                'name'  => 'Alliance to Restore the Republic',
                'ships' => [
                    'edges' => [
                        [
                            'node' => [
                                'name' => 'X-Wing'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $this->assertQuery($expected, $query);
    }

    // fetches the first two ships of the rebels with a cursor

    public function testFetchesTheFirstTwoShipsOfTheRebelsWithACursor()
    {
        $query = '
        query MoreRebelShipsQuery {
          rebels {
            name,
            ships(first: 2) {
              edges {
                cursor,
                node {
                  name
                }
              }
            }
          }
        }
        ';

        $expected = [
            'rebels' => [
                'name'  => 'Alliance to Restore the Republic',
                'ships' => [
                    'edges' => [
                        [
                            'cursor' => 'YXJyYXljb25uZWN0aW9uOjA=',
                            'node'   => [
                                'name' => 'X-Wing'
                            ]
                        ],
                        [
                            'cursor' => 'YXJyYXljb25uZWN0aW9uOjE=',
                            'node'   => [
                                'name' => 'Y-Wing'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $this->assertQuery($expected, $query);
    }

    // fetches the next three ships of the rebels with a cursor

    public function testFetchesTheNextThreeShipsOfTheRebelsWithACursor()
    {
        $query = '
        query EndOfRebelShipsQuery {
          rebels {
            name,
            ships(first: 3 after: "YXJyYXljb25uZWN0aW9uOjE=") {
              edges {
                cursor,
                node {
                  name
                }
              }
            }
          }
        }
        ';

        $expected = [
            'rebels' => [
                'name'  => 'Alliance to Restore the Republic',
                'ships' => [
                    'edges' => [
                        [
                            'cursor' => 'YXJyYXljb25uZWN0aW9uOjI=',
                            'node'   => [
                                'name' => 'A-Wing'
                            ]
                        ],
                        [
                            'cursor' => 'YXJyYXljb25uZWN0aW9uOjM=',
                            'node'   => [
                                'name' => 'Millenium Falcon'
                            ]
                        ],
                        [
                            'cursor' => 'YXJyYXljb25uZWN0aW9uOjQ=',
                            'node'   => [
                                'name' => 'Home One'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $this->assertQuery($expected, $query);
    }

    // fetches no ships of the rebels at the end of connection

    public function testFetchesNoShipsOfTheRebelsAtTheEndOfConnection()
    {
        $query = '
        query RebelsQuery {
          rebels {
            name,
            ships(first: 3 after: "YXJyYXljb25uZWN0aW9uOjQ=") {
              edges {
                cursor,
                node {
                  name
                }
              }
            }
          }
        }
        ';

        $expected = [
            'rebels' => [
                'name'  => 'Alliance to Restore the Republic',
                'ships' => ['edges' => []]
            ]
        ];

        $this->assertQuery($expected, $query);
    }

    // identifies the end of the list

    public function testIdentifiesTheEndOfTheList()
    {
        $query = '
        query EndOfRebelShipsQuery {
          rebels {
            name,
            originalShips: ships(first: 2) {
              edges {
                node {
                  name
                }
              }
              pageInfo {
                hasNextPage
              }
            }
            moreShips: ships(first: 3 after: "YXJyYXljb25uZWN0aW9uOjE=") {
              edges {
                node {
                  name
                }
              }
              pageInfo {
                hasNextPage
              }
            }
          }
        }
        ';

        $expected = [
            'rebels' => [
                'name'          => 'Alliance to Restore the Republic',
                'originalShips' => [
                    'edges'    => [
                        [
                            'node' => [
                                'name' => 'X-Wing'
                            ]
                        ],
                        [
                            'node' => [
                                'name' => 'Y-Wing'
                            ]
                        ]
                    ],
                    'pageInfo' => [
                        'hasNextPage' => true
                    ]
                ],
                'moreShips'     => [
                    'edges'    => [
                        [
                            'node' => [
                                'name' => 'A-Wing'
                            ]
                        ],
                        [
                            'node' => [
                                'name' => 'Millenium Falcon'
                            ]
                        ],
                        [
                            'node' => [
                                'name' => 'Home One'
                            ]
                        ]
                    ],
                    'pageInfo' => [
                        'hasNextPage' => false
                    ]
                ]
            ]
        ];

        $this->assertQuery($expected, $query);
    }

    private function assertQuery($expected, $query)
    {
        foreach ($this->schemas as $schema) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $result = graphql($schema, $query);

            $this->assertEquals(['data' => $expected], $result);
        }
    }
}
