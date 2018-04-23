<?php

namespace Digia\GraphQL\Relay\Test\Functional;

use Digia\GraphQL\Relay\ConnectionArguments;
use Digia\GraphQL\Relay\StoreNodeInterface;

class Ship implements StoreNodeInterface
{

    protected $id;
    protected $name;
    protected $cursor;

    /**
     * Ship constructor.
     *
     * @param string $id
     * @param string $name
     * @param string $cursor
     */
    public function __construct(string $id, string $name, string $cursor)
    {
        $this->id = $id;
        $this->name = $name;
        $this->cursor = $cursor;
    }

    /**
     * @inheritdoc
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getCursor(ConnectionArguments $arguments): string
    {
        return $this->cursor;
    }
}
