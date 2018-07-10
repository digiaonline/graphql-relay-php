<?php

namespace Digia\GraphQL\Relay;

interface StoreInterface
{

    /**
     * @param string              $cursor
     * @param ConnectionArguments $arguments
     * @return array
     */
    public function findAfterCursor(string $cursor, ConnectionArguments $arguments): array;

    /**
     * @param string              $cursor
     * @param ConnectionArguments $arguments
     * @return array
     */
    public function findBeforeCursor(string $cursor, ConnectionArguments $arguments): array;

    /**
     * @param int                 $first
     * @param ConnectionArguments $arguments
     * @return array
     */
    public function findFirst(int $first, ConnectionArguments $arguments): array;

    /**
     * @param int                 $last
     * @param ConnectionArguments $arguments
     * @return array
     */
    public function findLast(int $last, ConnectionArguments $arguments): array;

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
