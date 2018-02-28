<?php

namespace Tanigami\DomainEvent\Domain\Model;

use DateTimeImmutable;

abstract class DomainEvent
{
    /**
     * @var DateTimeImmutable
     */
    protected $occurredAt;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->occurredAt = new DateTimeImmutable;
    }

    /**
     * @return DateTimeImmutable
     */
    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
