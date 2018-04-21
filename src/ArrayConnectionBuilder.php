<?php

namespace Digia\GraphQL\Relay;

class ArrayConnectionBuilder extends AbstractConnectionBuilder
{

    protected const PREFIX = 'arrayconnection:';

    /**
     * @var array
     */
    protected $arraySlice;

    /**
     * @var ConnectionArguments
     */
    protected $arguments;

    /**
     * @var int
     */
    protected $sliceStart;

    /**
     * @var int
     */
    protected $arrayLength;

    /**
     * ArrayConnectionBuilder constructor.
     *
     * @param array               $arraySlice
     * @param ConnectionArguments $arguments
     * @param int                 $sliceStart
     * @param int                 $arrayLength
     */
    protected function __construct(
        array $arraySlice,
        ConnectionArguments $arguments,
        int $sliceStart,
        int $arrayLength
    ) {
        parent::__construct($arguments);

        $this->arraySlice  = $arraySlice;
        $this->sliceStart  = $sliceStart;
        $this->arrayLength = $arrayLength;
    }

    /**
     * @inheritdoc
     */
    public function getData(): iterable
    {
        return $this->arraySlice;
    }

    /**
     * @inheritdoc
     */
    public function getOffset(): int
    {
        return $this->sliceStart;
    }

    /**
     * @return int
     */
    public function getTotalCount(): int
    {
        return \count($this->arraySlice);
    }

    /**
     * @inheritdoc
     */
    public function createEdges(array $data, int $startOffset): array
    {
        $offset = 0;
        return \array_map(function ($value) use ($startOffset, &$offset): Edge {
            return new Edge($this->offsetToCursor($startOffset + $offset++), $value);
        }, $data);
    }

    /**
     * @param string $cursor
     * @return int|null
     */
    protected function getOffsetForCursor(string $cursor): ?int
    {
        return $this->cursorToOffset($cursor);
    }

    /**
     * Creates the cursor string from an offset.
     *
     * @param int $offset
     * @return string
     */
    protected function offsetToCursor(int $offset): string
    {
        return \base64_encode(self::PREFIX . $offset);
    }

    /**
     * Rederives the offset from the cursor string.
     *
     * @param string $cursor
     * @return int|null
     */
    protected function cursorToOffset(string $cursor): ?int
    {
        return (int)\substr($this->decodeCursor($cursor), \strlen(self::PREFIX));
    }

    /**
     * A simple function that accepts an array and connection arguments, and returns
     * a connection object for use in GraphQL. It uses array offsets as pagination,
     * so pagination will only work if the array is static.
     *
     * @param array               $data
     * @param ConnectionArguments $arguments
     * @return ConnectionInterface
     * @throws RelayException
     */
    public static function fromArray(array $data, ConnectionArguments $arguments): ConnectionInterface
    {
        return static::fromArraySlice($data, $arguments, 0, \count($data));
    }

    /**
     * Given a slice (subset) of an array, returns a connection object for use in
     * GraphQL.
     * This function is similar to `connectionFromArray`, but is intended for use
     * cases where you know the cardinality of the connection, consider it too large
     * to materialize the entire array, and instead wish pass in a slice of the
     * total result large enough to cover the range specified in `args`.
     *
     * @param array               $arraySlice
     * @param ConnectionArguments $arguments
     * @param int                 $sliceStart
     * @param int                 $arrayLength
     * @return ConnectionInterface
     * @throws RelayException
     */
    public static function fromArraySlice(
        array $arraySlice,
        ConnectionArguments $arguments,
        int $sliceStart,
        int $arrayLength
    ): ConnectionInterface {
        return (new static($arraySlice, $arguments, $sliceStart, $arrayLength))->build();
    }
}
