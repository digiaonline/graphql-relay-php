<?php

namespace Digia\GraphQL\Relay\Test;

use Digia\GraphQL\Relay\ConnectionArguments;
use Digia\GraphQL\Relay\StoreInterface;

class ShipStore implements StoreInterface
{

    /**
     * @var Ship[]
     */
    protected $data;

    /**
     * ShipStore constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @inheritdoc
     */
    public function findAfterCursor(string $cursor, ConnectionArguments $arguments): iterable
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function findBeforeCursor(string $cursor, ConnectionArguments $arguments): iterable
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function findFirst(ConnectionArguments $arguments): iterable
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function findLast(ConnectionArguments $arguments): iterable
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function getTotalCount(): int
    {
        return \count($this->data);
    }
}
