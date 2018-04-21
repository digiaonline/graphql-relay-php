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
     * @param ConnectionArguments $arguments
     * @return iterable
     */
    public function findFirst(ConnectionArguments $arguments): iterable;

    /**
     * @param ConnectionArguments $arguments
     * @return iterable
     */
    public function findLast(ConnectionArguments $arguments): iterable;

    /**
     * @return int
     */
    public function getTotalCount(): int;
}
