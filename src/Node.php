<?php

namespace Digia\GraphQL\Relay;

class Node
{
    private $type;
    private $id;

    private function __construct(string $type, string $id)
    {
        $this->type = $type;
        $this->id = $id;
    }

    public static function toGlobalId(string $type, string $id): string
    {
        return base64_encode("${type}:${id}");
    }

    public static function fromGlobalId(string $id): Node
    {
        $decoded = base64_decode($id, true);
        if (!$decoded) {
            throw new \InvalidArgumentException('ID must be a valid base 64 string');
        }

        $elements = explode(':', $decoded, 2);
        if (\count($elements) !== 2) {
            throw new \InvalidArgumentException('ID was not correctly formed');
        }

        return new self($elements[0], $elements[1]);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getId(): string
    {
        return $this->id;
    }
}