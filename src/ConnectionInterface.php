<?php

namespace Digia\GraphQL\Relay;

interface ConnectionInterface
{

    /**
     * @return array
     */
    public function getEdges(): array;

    /**
     * @return PageInfo
     */
    public function getPageInfo(): PageInfo;
}
