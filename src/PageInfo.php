<?php

namespace Digia\GraphQL\Relay;

class PageInfo
{

    /**
     * @var string|null
     */
    protected $startCursor;

    /**
     * @var string|null
     */
    protected $endCursor;

    /**
     * @var bool
     */
    protected $hasPreviousPage;

    /**
     * @var bool
     */
    protected $hasNextPage;

    /**
     * PageInfo constructor.
     *
     * @param null|string $startCursor
     * @param null|string $endCursor
     * @param bool        $hasPreviousPage
     * @param bool        $hasNextPage
     */
    public function __construct(
        ?string $startCursor = null,
        ?string $endCursor = null,
        bool $hasPreviousPage = false,
        bool $hasNextPage = false
    ) {
        $this->startCursor     = $startCursor;
        $this->endCursor       = $endCursor;
        $this->hasPreviousPage = $hasPreviousPage;
        $this->hasNextPage     = $hasNextPage;
    }

    /**
     * @return null|string
     */
    public function getStartCursor(): ?string
    {
        return $this->startCursor;
    }

    /**
     * @return null|string
     */
    public function getEndCursor(): ?string
    {
        return $this->endCursor;
    }

    /**
     * @return bool
     */
    public function hasPreviousPage(): bool
    {
        return $this->hasPreviousPage;
    }

    /**
     * @return bool
     */
    public function hasNextPage(): bool
    {
        return $this->hasNextPage;
    }
}
