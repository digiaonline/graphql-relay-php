# GraphQL Relay

[![Build Status](https://travis-ci.org/digiaonline/graphql-relay-php.svg?branch=master)](https://travis-ci.org/digiaonline/graphql-relay-php)
[![Coverage Status](https://coveralls.io/repos/github/digiaonline/graphql-relay-php/badge.svg?branch=master)](https://coveralls.io/github/digiaonline/graphql-relay-php?branch=master)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/digiaonline/graphql-relay-php/master/LICENSE)

Relay support for our [GraphQL implementation](https://github.com/digiaonline/graphql-php/).

## Requirements

* PHP >= 7.1

## Usage

### Installation

Run the following command to install the package through Composer:

```sh
composer require digiaonline/graphql-relay:dev-master
```

### Example

Executing this script:

```php
use function Digia\GraphQL\buildSchema;
use function Digia\GraphQL\graphql;

$source = file_get_contents(__DIR__ . '/star-wars.graphqls');

$schema = buildSchema($source, [
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
            $data      = getShips($faction);
            $arguments = ConnectionArguments::fromArray($args);
            return ArrayConnectionBuilder::fromArray($data, $arguments);
        }
    ]
]);

$result = graphql($schema, '
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
}');

print_r($result);
```

Produces the following output:

```php
Array
(
    [rebels] => Array
        (
            [name] => Alliance to Restore the Republic
            [ships] => Array
                (
                    [edges] => Array
                        (
                            [0] => Array
                                (
                                    [node] => Array
                                        (
                                            [name] => X-Wing
                                        )
                                        
                                )
                                
                        )
                        
                )
                
        )
        
)
```

The schema definition used looks like this:

```graphql schema
"A connection to a list of items."
interface Connection {
    "A list of edges."
    edges: [Edge]
    "Information to aid in pagination."
    pageInfo: PageInfo!
}

"An edge in a connection."
interface Edge {
    "A cursor for use in pagination."
    cursor: String!
    "The item at the end of the edge."
    node: Node
}

"An object with an ID."
interface Node {
    "ID of the object."
    id: ID!
}

"Information about pagination in a connection."
type PageInfo {
    "When paginating forwards, are there more items?"
    hasPreviousPage: Boolean!
    "When paginating backwards, are there more items?"
    hasNextPage: Boolean!
    "When paginating backwards, the cursor to continue."
    endCursor: String
    "When paginating forwards, the cursor to continue."
    startCursor: String
}

type Faction implements Node {
    "The ID of an object."
    id: ID!
    "The name of the faction."
    name: String
    "The ships used by the faction."
    ships(after: String, before: String, first: Int, last: Int): ShipConnection
}

"A ship in the Star Wars saga"
type Ship implements Node {
    "The ID of an object."
    id: ID!
    "The name of the ship."
    name: String
}

type ShipConnection implements Connection {
    edges: [ShipEdge]
    pageInfo: PageInfo!
}

type ShipEdge implements Edge {
    cursor: String!
    node: Ship
}

type Query {
    rebels: Faction
    empire: Faction
    node(id: ID!): Node
}

schema {
    query: Query
}
```

### Node root field

For implementing the [Node root field](https://facebook.github.io/relay/graphql/objectidentification.htm#sec-Node-root-field)
a convenience class is provided:


#### Convert Type and ID to Global ID

```php
$nodeId = Node::toGlobalId('Ship', '1');
```

returns a global ID which can be passed to the node root:

```
U2hpcDox
```

#### Convert Global ID back to type and ID

```php
$node = Node::fromGlobalId('U2hpcDox');
```

returns an object which can be queried:

```php
$node->getType(); //Ship
$node->getId(); //1
```

#### Node root resolver

For an example of how to implement the node root resolver please check the [StarWarsConnectionTest.php](tests/Functional/StarWarsConnectionTest.php)


## Contributing

Please read our [guidelines](.github/CONTRIBUTING.md).

## License

See [LICENSE](LICENSE).
