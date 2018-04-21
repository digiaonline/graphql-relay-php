<?php

namespace Digia\GraphQL\Relay;

interface ConnectionBuilderInterface
{

    /**
     * @return iterable
     */
    public function getData(): iterable;

    /**
     * @return int
     */
    public function getOffset(): int;

    /**
     * Returns the total number of items in the connection.
     *
     * @return int
     */
    public function getTotalCount(): int;

    /**
     * Creates the edges for the connection.
     *
     * @param array $data
     * @param int   $startOffset
     * @param int   $endOffset
     * @return Edge[]
     */
    public function createEdges(array $data, int $startOffset): array;
}
