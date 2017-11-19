<?php

namespace Tanigami\DomainEvent;

use DateTimeImmutable;

abstract class DomainEvent
{
    /**
     * @var DateTimeImmutable
     */
    protected $occurredAt;

    /**
     * @param DateTimeImmutable $occurredAt
     */
    public function __construct(DateTimeImmutable $occurredAt = null)
    {
        if (null === $occurredAt) {
            $occurredAt = new DateTimeImmutable;
        }
        $this->occurredAt = $occurredAt;
    }

    /**
     * @return DateTimeImmutable
     */
    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}