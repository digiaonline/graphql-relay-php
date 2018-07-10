<?php

namespace Digia\GraphQL\Relay\Test\Functional;

use Digia\GraphQL\Relay\ArrayConnectionBuilder;
use Digia\GraphQL\Relay\ConnectionArguments;
use Digia\GraphQL\Relay\StoreConnectionBuilder;
use function Digia\GraphQL\buildSchema;

function starWarsSchemaWithArrayConnection()
{
    $source = \file_get_contents(__DIR__ . '/schema.graphqls');

    if ($source === false) {
        throw new \RuntimeException('File not found');
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    return buildSchema($source, [
        'Query'   => [
            'rebels' => function () {
                return rebels();
            },
            'empire' => function () {
                return empire();
            }
        ],
        'Faction' => [
            'ships' => function ($faction, $args) {
                $data = \array_map(function ($id) {
                    return getShip($id);
                }, $faction['ships']);

                $arguments = ConnectionArguments::fromArray($args);

                return ArrayConnectionBuilder::fromArray($data, $arguments);
            }
        ]
    ]);
}

function starWarsSchemaWithStoreConnection()
{
    $source = \file_get_contents(__DIR__ . '/schema.graphqls');

    if ($source === false) {
        throw new \RuntimeException('File not found');
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    return buildSchema($source, [
        'Query'   => [
            'rebels' => function () {
                return rebels();
            },
            'empire' => function () {
                return empire();
            }
        ],
        'Faction' => [
            'ships' => function ($faction, $args) {
                $data = \array_reduce($faction['ships'], function ($data, $id) {
                    static $index = 0;
                    ['id' => $shipId, 'name' => $shipName] = getShip($id);
                    $cursor        = 'arrayconnection:' . $index++;
                    $data[$cursor] = new Ship($shipId, $shipName, $cursor);
                    return $data;
                }, []);

                $arguments = ConnectionArguments::fromArray($args);

                return StoreConnectionBuilder::forStore(new ShipStore($data), $arguments);
            }
        ]
    ]);
}
