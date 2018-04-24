<?php

namespace Digia\GraphQL\Relay;

interface StoreInterface
{

    /**
     * @param string              $cursor
     * @param ConnectionArguments $arguments
     * @return iterable
     */
    public function findAfterCursor(string $cursor, ConnectionArguments $arguments): iterable;

    /**
     * @param string              $cursor
     * @param ConnectionArguments $arguments
     * @return iterable
     */
    public function findBeforeCursor(string $cursor, ConnectionArguments $arguments): iterable;

    /**
     * @param int                 $first
     * @param ConnectionArguments $arguments
     * @return iterable
     */
    public function findFirst(int $first, ConnectionArguments $arguments): iterable;

    /**
     * @param int                 $last
     * @param ConnectionArguments $arguments
     * @return iterable
     */
    public function findLast(int $last, ConnectionArguments $arguments): iterable;

    /**
     * @return int
     */
    public function getTotalCount(): int;

    /**
     * @param mixed               $node
     * @param ConnectionArguments $arguments
     * @return string
     */
    public function createCursor($node, ConnectionArguments $arguments): string;
}
