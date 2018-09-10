<?php

namespace Digia\GraphQL\Relay;

final class Node
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $id;

    /**
     * @param string $type
     * @param string $id
     */
    private function __construct(string $type, string $id)
    {
        $this->type = $type;
        $this->id = $id;
    }

    /**
     * @param string $type
     * @param string $id
     * @return string
     */
    public static function toGlobalId(string $type, string $id): string
    {
        return \base64_encode("${type}:${id}");
    }

    /**
     * @param string $id
     * @return Node
     */
    public static function fromGlobalId(string $id): Node
    {
        $decoded = \base64_decode($id, true);

        if (!$decoded) {
            throw new \InvalidArgumentException('ID must be a valid base 64 string');
        }

        $elements = explode(':', $decoded);
        if (\count($elements) !== 2) {
            throw new \InvalidArgumentException('ID was not correctly formed');
        }

        return new self($elements[0], $elements[1]);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}