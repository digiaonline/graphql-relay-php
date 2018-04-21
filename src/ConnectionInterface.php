<?php

namespace Digia\GraphQL\Relay;

interface ConnectionInterface
{

    /**
     * @return iterable
     */
    public function getEdges(): iterable;

    /**
     * @return PageInfo
     */
    public function getPageInfo(): PageInfo;
}
