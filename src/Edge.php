<?php

namespace Digia\GraphQL\Relay;

class Edge
{

    /**
     * @var string
     */
    protected $cursor;

    /**
     * @var mixed
     */
    protected $node;

    /**
     * Edge constructor.
     *
     * @param string $cursor
     * @param mixed  $node
     */
    public function __construct(string $cursor, $node)
    {
        $this->cursor = $cursor;
        $this->node   = $node;
    }

    /**
     * @return string
     */
    public function getCursor(): string
    {
        return $this->cursor;
    }

    /**
     * @return mixed
     */
    public function getNode()
    {
        return $this->node;
    }
}
