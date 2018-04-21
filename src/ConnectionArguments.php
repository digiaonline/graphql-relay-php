<?php

namespace Digia\GraphQL\Relay;

class ConnectionArguments
{

    /**
     * @var string|null
     */
    protected $after;

    /**
     * @var string|null
     */
    protected $before;

    /**
     * @var int|null
     */
    protected $first;

    /**
     * @var int|null
     */
    protected $last;

    /**
     * ConnectionArguments constructor.
     *
     * @param null|string $after
     * @param null|string $before
     * @param int|null    $first
     * @param int|null    $last
     */
    public function __construct(?string $after = null, ?string $before = null, ?int $first = null, ?int $last = null)
    {
        $this->after  = $after;
        $this->before = $before;
        $this->first  = $first;
        $this->last   = $last;
    }

    /**
     * @return null|string
     */
    public function getAfter(): ?string
    {
        return $this->after;
    }

    /**
     * @return null|string
     */
    public function getBefore(): ?string
    {
        return $this->before;
    }

    /**
     * @return int|null
     */
    public function getFirst(): ?int
    {
        return $this->first;
    }

    /**
     * @return int|null
     */
    public function getLast(): ?int
    {
        return $this->last;
    }

    /**
     * @param array $arguments
     * @return ConnectionArguments
     */
    public static function fromArray(array $arguments): ConnectionArguments
    {
        return new ConnectionArguments(
            $arguments['after'] ?? null,
            $arguments['before'] ?? null,
            $arguments['first'] ?? null,
            $arguments['last'] ?? null
        );
    }
}
