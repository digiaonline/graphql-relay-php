<?php

namespace Digia\GraphQL\Relay;

interface StoreNodeInterface extends NodeInterface
{
    /**
     * @param ConnectionArguments $arguments
     * @return string
     */
    public function getCursor(ConnectionArguments $arguments): string;
}
