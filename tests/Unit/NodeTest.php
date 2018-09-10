<?php

namespace Digia\GraphQL\Relay\Test\Unit;

use Digia\GraphQL\Relay\Node;
use PHPUnit\Framework\TestCase;

final class NodeTest extends TestCase
{
    public function testConvertsTypeAndIdToGlobalId(): void
    {
        $nodeId = Node::toGlobalId('Ship', '1');

        $this->assertSame('U2hpcDox', $nodeId, 'Node ID did not match');
    }

    public function testConvertsGlobalIdToTypeAndId(): void
    {
        $node = Node::fromGlobalId('U2hpcDox');

        $this->assertSame('Ship', $node->getType(), 'Type did not match');
        $this->assertSame('1', $node->getId(), 'Id did not match');
    }

    public function testThrowsExceptionIfNotValidBase64String(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('ID must be a valid base 64 string');

        Node::fromGlobalId('🦄');
    }

    public function testThrowsExceptionIfBadlyFormatted(): void
    {
        $this->expectExceptionMessage(\InvalidArgumentException::class);
        $this->expectExceptionMessage('ID was not correctly formed');

        $bad = \base64_encode('Ship11111::::::::::');
        Node::fromGlobalId($bad);
    }
}