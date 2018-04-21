<?php

namespace Digia\GraphQL\Relay\Test;

use Digia\GraphQL\Relay\ConnectionArguments;
use Digia\GraphQL\Relay\NodeInterface;

class Ship implements NodeInterface
{

    public $id;
    public $name;
    public $cursor;

    /**
     * Ship constructor.
     *
     * @param $id
     * @param $name
     * @param $cursor
     */
    public function __construct($id, $name, $cursor)
    {
        $this->id     = $id;
        $this->name   = $name;
        $this->cursor = $cursor;
    }

    /**
     * @param ConnectionArguments $arguments
     * @return string
     */
    public function createCursor(ConnectionArguments $arguments): string
    {
        return $this->cursor;
    }
}
