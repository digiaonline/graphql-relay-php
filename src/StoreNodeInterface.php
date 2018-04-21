<?php

namespace Digia\GraphQL\Relay;

interface StoreNodeInterface extends NodeInterface
{
    /**
     * @return string
     */
    public function getCursor(): string;
}
