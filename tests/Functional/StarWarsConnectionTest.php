<?php

namespace Digia\GraphQL\Relay\Test\Functional;

use Digia\GraphQL\Relay\Node;
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
            name
            ships(first: 1) {
              edges {
                node {
                  id
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
                                'id' => 'U2hpcDox',
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
            id
            name
            ships(first: 2) {
              edges {
                cursor
                node {
                  id
                  name
                }
              }
            }
          }
        }
        ';

        $expected = [
            'rebels' => [
                'id' => 'RmFjdGlvbjox',
                'name'  => 'Alliance to Restore the Republic',
                'ships' => [
                    'edges' => [
                        [
                            'cursor' => 'YXJyYXljb25uZWN0aW9uOjA=',
                            'node'   => [
                                'id' => 'U2hpcDox',
                                'name' => 'X-Wing'
                            ]
                        ],
                        [
                            'cursor' => 'YXJyYXljb25uZWN0aW9uOjE=',
                            'node'   => [
                                'id' => 'U2hpcDoy',
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
            id
            name
            ships(first: 3 after: "YXJyYXljb25uZWN0aW9uOjE=") {
              edges {
                cursor
                node {
                  id
                  name
                }
              }
            }
          }
        }
        ';

        $expected = [
            'rebels' => [
                'id' => 'RmFjdGlvbjox',
                'name'  => 'Alliance to Restore the Republic',
                'ships' => [
                    'edges' => [
                        [
                            'cursor' => 'YXJyYXljb25uZWN0aW9uOjI=',
                            'node'   => [
                                'id' => 'U2hpcDoz',
                                'name' => 'A-Wing'
                            ]
                        ],
                        [
                            'cursor' => 'YXJyYXljb25uZWN0aW9uOjM=',
                            'node'   => [
                                'id' => 'U2hpcDo0',
                                'name' => 'Millenium Falcon'
                            ]
                        ],
                        [
                            'cursor' => 'YXJyYXljb25uZWN0aW9uOjQ=',
                            'node'   => [
                                'id' => 'U2hpcDo1',
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
            id
            name
            ships(first: 3 after: "YXJyYXljb25uZWN0aW9uOjQ=") {
              edges {
                cursor
                node {
                  id
                  name
                }
              }
            }
          }
        }
        ';

        $expected = [
            'rebels' => [
                'id' => 'RmFjdGlvbjox',
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
            id
            name
            originalShips: ships(first: 2) {
              edges {
                node {
                  id
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
                  id
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
                'id' => 'RmFjdGlvbjox',
                'name'          => 'Alliance to Restore the Republic',
                'originalShips' => [
                    'edges'    => [
                        [
                            'node' => [
                                'id' => 'U2hpcDox',
                                'name' => 'X-Wing'
                            ]
                        ],
                        [
                            'node' => [
                                'id' => 'U2hpcDoy',
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
                                'id' => 'U2hpcDoz',
                                'name' => 'A-Wing'
                            ]
                        ],
                        [
                            'node' => [
                                'id' => 'U2hpcDo0',
                                'name' => 'Millenium Falcon'
                            ]
                        ],
                        [
                            'node' => [
                                'id' => 'U2hpcDo1',
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

    public function testAllowsQueryingShipByNodeId(): void
    {
        $id = Node::toGlobalId('Ship', '1');

        $query = "
        query NodeQuery {
          node(id: \"${id}\") {
            ... on Ship {
              id
              name
            }
          }
        }
        ";

        $expected = [
            'node' => [
                'id' => $id,
                'name' => 'X-Wing',
            ],
        ];

        $this->assertQuery($expected, $query);
    }

    public function testAllowsQueryingFactionByNodeId(): void
    {
        $id = Node::toGlobalId('Faction', '1');

        $query = '
        query NodeQuery {
          node(id: "' .$id .'") {
            ... on Faction {
              id
              name
            }
          }
        }
        ';

        $expected = [
            'node' => [
                'id' => $id,
                'name' => 'Alliance to Restore the Republic',
            ],
        ];

        $this->assertQuery($expected, $query);
    }

    private function assertQuery($expected, $query)
    {
        foreach ($this->schemas as $schema) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $result = graphql($schema, $query);

            $this->assertSame(['data' => $expected], $result);
        }
    }
}
