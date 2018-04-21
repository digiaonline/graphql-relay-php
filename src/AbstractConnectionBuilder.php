<?php

namespace Digia\GraphQL\Relay;

abstract class AbstractConnectionBuilder
{

    /**
     * @var ConnectionArguments
     */
    protected $arguments;

    /**
     * @return iterable
     */
    abstract protected function getData(): iterable;

    /**
     * @return int
     */
    abstract protected function getOffset(): int;

    /**
     * Returns the total number of items in the connection.
     *
     * @return int
     */
    abstract protected function getTotalCount(): int;

    /**
     * Creates the edges for the connection.
     *
     * @param array $data
     * @param int   $startOffset
     * @param int   $endOffset
     * @return Edge[]
     */
    abstract protected function createEdges(array $data, int $startOffset): array;

    /**
     * @param string $cursor
     * @return int|null
     */
    abstract protected function cursorToOffset(string $cursor): ?int;

    /**
     * AbstractConnectionBuilder constructor.
     *
     * @param ConnectionArguments $arguments
     */
    protected function __construct(ConnectionArguments $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * @return ConnectionInterface
     * @throws RelayException
     */
    protected function build(): ConnectionInterface
    {
        $arguments    = $this->arguments;
        $data         = $this->getData();
        $sliceStart   = $this->getOffset();
        $totalCount   = $this->getTotalCount();
        $sliceLength  = \count($data);
        $sliceEnd     = $sliceStart + $sliceLength;
        $after        = $arguments->getAfter();
        $before       = $arguments->getBefore();
        $first        = $arguments->getFirst();
        $last         = $arguments->getLast();
        $beforeOffset = $this->getOffsetWithDefault($before, $totalCount);
        $afterOffset  = $this->getOffsetWithDefault($after, -1);
        $startOffset  = \max($sliceStart - 1, $afterOffset, -1) + 1;
        $endOffset    = \min($sliceEnd, $beforeOffset, $totalCount);

        if (null !== $first) {
            if ($first < 0) {
                throw new RelayException('Argument "first" must be a non-negative integer');
            }

            $endOffset = \min($endOffset, $startOffset + $first);
        }

        if (null !== $last) {
            if ($last < 0) {
                throw new RelayException('Argument "last" must be a non-negative integer');
            }

            $startOffset = \max($startOffset, $endOffset - $last);
        }

        // If supplied slice is too large, trim it down before mapping over it.
        $offset = \max($startOffset - $sliceStart, 0);
        $length = $sliceLength - ($sliceEnd - $endOffset) - $offset;
        $slice  = \array_slice($data, $offset, $length);

        $edges = $this->createEdges($slice, $startOffset);

        $firstEdge  = $edges[0] ?? null;
        $lastEdge   = $edges[\count($edges) - 1] ?? null;
        $lowerBound = null !== $after ? ($afterOffset + 1) : 0;
        $upperBound = null !== $before ? $beforeOffset : $totalCount;

        $startCursor     = null !== $firstEdge ? $firstEdge->getCursor() : null;
        $endCursor       = null !== $lastEdge ? $lastEdge->getCursor() : null;
        $hasPreviousPage = null !== $last ? $startOffset > $lowerBound : false;
        $hasNextPage     = null !== $first ? $endOffset < $upperBound : false;

        $pageInfo = new PageInfo($startCursor, $endCursor, $hasPreviousPage, $hasNextPage);

        return new Connection($edges, $pageInfo);
    }

    /**
     * @param null|string $cursor
     * @param int         $default
     * @return int|null
     */
    protected function getOffsetWithDefault(?string $cursor, int $default): ?int
    {
        if (null === $cursor) {
            return $default;
        }

        return $this->cursorToOffset($cursor) ?? $default;
    }

    /**
     * @param string $cursor
     * @return string
     */
    protected function encodeCursor(string $cursor): string
    {
        return \base64_encode($cursor);
    }

    /**
     * @param string $cursor
     * @return string
     */
    protected function decodeCursor(string $cursor): string
    {
        return \base64_decode($cursor);
    }
}
