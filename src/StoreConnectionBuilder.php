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
     * @return array
     * @throws RelayException
     */
    protected function fetchData(): array
    {
        $arguments = $this->arguments;

        $first = $arguments->getFirst();

        if (null !== $first) {
            $after = $arguments->getAfter();

            return null !== $after
                ? $this->store->findAfterCursor($this->decodeCursor($after), $arguments)
                : $this->store->findFirst($arguments);
        }

        $last = $arguments->getLast();

        if (null !== $last) {
            $before = $arguments->getBefore();

            return null !== $before
                ? $this->store->findBeforeCursor($this->decodeCursor($before), $arguments)
                : $this->store->findLast($arguments);
        }

        throw new RelayException('You must provide a `first` or `last` value to properly paginate connections.');
    }

    /**
     * @inheritdoc
     */
    public function createEdges(array $data, int $startOffset): array
    {
        return \array_map(function (NodeInterface $node): Edge {
            return new Edge($this->encodeCursor($node->createCursor($this->arguments)), $node);
        }, $data);
    }

    /**
     * @inheritdoc
     * @throws RelayException
     */
    public function getData(): iterable
    {
        if (!isset($this->data)) {
            $this->data = $this->fetchData();
        }

        return $this->data;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return 0;
    }

    /**
     * @inheritdoc
     */
    public function getTotalCount(): int
    {
        return $this->store->getTotalCount();
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
        return \array_reduce($this->getData(), function ($cursors, NodeInterface $node): array {
            static $index = 0;
            $cursors[$this->encodeCursor($node->createCursor($this->arguments))] = $index++;
            return $cursors;
        }, []);
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
