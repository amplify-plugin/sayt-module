<?php

namespace Amplify\System\Sayt\Classes;

class Suggestion implements \JsonSerializable
{
    private $m_node;

    private ?string $m_val;
    private ?int $m_start;
    private ?int $m_end;
    private ?int $m_weight;

    public function __construct($node)
    {
        $this->m_node = $node;

        $this->processNode();
    }

    private function processNode(): void
    {
        $this->m_val = $this->m_node?->val;
        $this->m_start = $this->m_node?->start;
        $this->m_end = $this->m_node?->end;
        $this->m_weight = $this->m_node?->wgt;
    }

    public function getValue()
    {
        return $this->m_val;
    }

    public function getNode()
    {
        return $this->m_node;
    }

    public function getStart()
    {
        return $this->m_start;
    }

    public function getEnd()
    {
        return $this->m_end;
    }

    public function getWeight()
    {
        return $this->m_weight;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4
     */
    public function jsonSerialize(): mixed
    {
        return $this->m_node;
    }
}