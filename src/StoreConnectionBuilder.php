<?php

namespace Digia\GraphQL\Relay;

class StoreConnectionBuilder extends AbstractConnectionBuilder
{

    /**
     * @var StoreInterface
     */
    protected $store;

    /**
     * @var iterable
     */
    protected $data;

    /**
     * @var int[]
     */
    protected $cursorMap;

    /**
     * StoreConnectionBuilder constructor.
     *
     * @param StoreInterface      $store
     * @param ConnectionArguments $arguments
     */
    protected function __construct(StoreInterface $store, ConnectionArguments $arguments)
    {
        parent::__construct($arguments);

        $this->store = $store;
    }

    /**
     * @return iterable
     * @throws RelayException
     */
    protected function fetchData(): iterable
    {
        $arguments = $this->arguments;

        $first = $arguments->getFirst();

        if (null !== $first) {
            $after = $arguments->getAfter();

            return null !== $after
                ? $this->store->findAfterCursor($this->decodeCursor($after), $arguments)
                : $this->store->findFirst($first, $arguments);
        }

        $last = $arguments->getLast();

        if (null !== $last) {
            $before = $arguments->getBefore();

            return null !== $before
                ? $this->store->findBeforeCursor($this->decodeCursor($before), $arguments)
                : $this->store->findLast($last, $arguments);
        }

        throw new RelayException('You must provide a `first` or `last` value to properly paginate connections.');
    }

    /**
     * @inheritdoc
     * @throws RelayException
     */
    protected function getData(): iterable
    {
        if (!isset($this->data)) {
            $this->data = $this->fetchData();
        }

        return $this->data;
    }

    /**
     * @return int
     */
    protected function getOffset(): int
    {
        return 0;
    }

    /**
     * @inheritdoc
     */
    protected function getTotalCount(): int
    {
        return $this->store->getTotalCount();
    }

    /**
     * @inheritdoc
     */
    protected function createEdges(array $data, int $startOffset): array
    {
        return \array_map(function ($node): Edge {
            return new Edge($this->createCursor($node), $node);
        }, $data);
    }

    /**
     * @inheritdoc
     * @throws RelayException
     */
    protected function cursorToOffset(string $cursor): ?int
    {
        return $this->getCursorMap()[$cursor] ?? null;
    }

    /**
     * @return array
     * @throws RelayException
     */
    protected function getCursorMap(): array
    {
        if (!isset($this->cursorMap)) {
            $this->cursorMap = $this->createCursorMap();
        }

        return $this->cursorMap;
    }

    /**
     * @return array
     * @throws RelayException
     */
    protected function createCursorMap(): array
    {
        return \array_reduce($this->getData(), function ($cursors, $node): array {
            static $index = 0;
            $cursors[$this->createCursor($node)] = $index++;
            return $cursors;
        }, []);
    }

    /**
     * @param $node
     * @return string
     */
    protected function createCursor($node): string
    {
        return $this->encodeCursor($this->store->createCursor($node, $this->arguments));
    }

    /**
     * @param StoreInterface      $store
     * @param ConnectionArguments $arguments
     * @return ConnectionInterface
     * @throws RelayException
     */
    public static function forStore(StoreInterface $store, ConnectionArguments $arguments): ConnectionInterface
    {
        return (new static($store, $arguments))->build();
    }
}
