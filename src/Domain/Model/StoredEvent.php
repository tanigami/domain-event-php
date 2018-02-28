<?php

namespace Tanigami\DomainEvent\Domain\Model;

use DateTimeImmutable;

class StoredEvent
{
    /**
     * @var int|null
     */
    protected $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $body;

    /**
     * @var DateTimeImmutable
     */
    private $occurredAt;

    /**
     * @param string $name
     * @param string $body
     * @param DateTimeImmutable $occurredAt
     */
    public function __construct(string $name, string $body, DateTimeImmutable $occurredAt)
    {
        $this->name = $name;
        $this->body = $body;
        $this->occurredAt = $occurredAt;
    }

    /**
     * @return int|null
     */
    public function id(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function body(): string
    {
        return $this->body;
    }

    /**
     * @return DateTimeImmutable
     */
    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
