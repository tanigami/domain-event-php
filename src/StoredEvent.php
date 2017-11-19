<?php

namespace Tanigami\DomainEvent;

use DateTimeImmutable;

class StoredEvent extends DomainEvent
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $body;

    /**
     * @param string $name
     * @param string $body
     * @param DateTimeImmutable $occurredAt
     */
    public function __construct(string $name, string $body, DateTimeImmutable $occurredAt)
    {
        parent::__construct($occurredAt);
        $this->name = $name;
        $this->body = $body;
    }

    /**
     * @return int|null
     */
    public function id()
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
}
