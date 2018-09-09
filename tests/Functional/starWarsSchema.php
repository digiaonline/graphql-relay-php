<?php

namespace Digia\GraphQL\Relay\Test\Functional;

use Digia\GraphQL\Relay\ArrayConnectionBuilder;
use Digia\GraphQL\Relay\ConnectionArguments;
use Digia\GraphQL\Relay\Node;
use Digia\GraphQL\Relay\StoreConnectionBuilder;
use function Digia\GraphQL\buildSchema;

function getNodeId(string $type, $variable) {
    if (\is_object($variable) && method_exists($variable, 'getId')) {
        return Node::toGlobalId($type, $variable->getId());
    }

    if (\is_array($variable)) {
        return Node::toGlobalId($type, $variable['id']);
    }

    throw new \RuntimeException('Unable to get an ID from the variable');
}

function addType(string $type, $variable) {
    if (\is_object($variable)) {
        $variable->type = $type;

        return $variable;
    }

    if (\is_array($variable)) {
        $variable['type'] = $type;

        return $variable;
    }

    throw new \RuntimeException('Unable to set a type on the variable');
}

function nodeResolver($root, $args)
{
    $node = Node::fromGlobalId($args['id']);

    switch ($node->getType()) {
        case 'Ship':
            $entity = getShip($node->getId());
            break;
        case 'Faction':
            $entity = getFaction($node->getId());
            break;
        default:
            throw new \RuntimeException('No node resolver for type: ' . $node->getType());
    }

    return addType($node->getType(), $entity);
}

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
            },
            'node' => function ($root, $args) {
                return nodeResolver($root, $args);
            }
        ],
        'Faction' => [
            'id' => function ($root, $args) {
                return getNodeId('Faction', $root);
            },
            'ships' => function ($faction, $args) {
                $data = \array_map(function ($id) {
                    return getShip($id);
                }, $faction['ships']);

                $arguments = ConnectionArguments::fromArray($args);

                return ArrayConnectionBuilder::fromArray($data, $arguments);
            }
        ],
        'Ship' => [
            'id' => function ($root, $args) {
                return getNodeId('Ship', $root);
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
            },
            'node' => function ($root, $args) {
                return nodeResolver($root, $args);
            }
        ],
        'Faction' => [
            'id' => function ($root, $args) {
                return getNodeId('Faction', $root);
            },
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
        ],
        'Ship' => [
            'id' => function ($root, $args) {
                return getNodeId('Ship', $root);
            }
        ]
    ]);
}
