<?php

namespace Digia\GraphQL\Relay\Test\Functional;

use Digia\GraphQL\Relay\NodeInterface;

class Ship implements NodeInterface
{

    protected $id;
    protected $name;
    protected $cursor;

    /**
     * Ship constructor.
     *
     * @param string $id
     * @param string $name
     * @param string $cursor
     */
    public function __construct(string $id, string $name, string $cursor)
    {
        $this->id     = $id;
        $this->name   = $name;
        $this->cursor = $cursor;
    }

    /**
     * @inheritdoc
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCursor(): string
    {
        return $this->cursor;
    }
}
