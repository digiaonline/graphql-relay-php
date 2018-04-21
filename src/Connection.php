<?php

namespace Digia\GraphQL\Relay;

class Connection implements ConnectionInterface
{

    /**
     * @var Edge[]
     */
    protected $edges;

    /**
     * @var PageInfo
     */
    protected $pageInfo;

    /**
     * Connection constructor.
     *
     * @param Edge[]   $edges
     * @param PageInfo $pageInfo
     */
    public function __construct(array $edges, PageInfo $pageInfo)
    {
        $this->edges    = $edges;
        $this->pageInfo = $pageInfo;
    }

    /**
     * @return Edge[]
     */
    public function getEdges(): array
    {
        return $this->edges;
    }

    /**
     * @return PageInfo
     */
    public function getPageInfo(): PageInfo
    {
        return $this->pageInfo;
    }
}
