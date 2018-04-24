<?php

namespace Digia\GraphQL\Relay\Test\Functional;

use Digia\GraphQL\Relay\ConnectionArguments;
use Digia\GraphQL\Relay\StoreInterface;
use Digia\GraphQL\Relay\StoreNodeInterface;

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
        return \array_reverse($this->data);
    }

    /**
     * @inheritdoc
     */
    public function findFirst(int $first, ConnectionArguments $arguments): iterable
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function findLast(int $last, ConnectionArguments $arguments): iterable
    {
        return \array_reverse($this->data);
    }

    /**
     * @inheritdoc
     */
    public function getTotalCount(): int
    {
        return \count($this->data);
    }

    /**
     * @param Ship                $node
     * @param ConnectionArguments $arguments
     * @return string
     */
    public function createCursor($node, ConnectionArguments $arguments): string
    {
        return $node->getCursor();
    }
}
