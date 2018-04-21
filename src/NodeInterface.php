<?php

namespace Digia\GraphQL\Relay;

interface NodeInterface
{

    /**
     * @param ConnectionArguments $arguments
     * @return string
     */
    public function createCursor(ConnectionArguments $arguments): string;
}
