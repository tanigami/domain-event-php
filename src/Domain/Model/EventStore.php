<?php

namespace Tanigami\DomainEvent\Domain\Model;

/**
 * OK
 */
interface EventStore
{
    /**
     * @param DomainEvent $domainEvent
     */
    public function append(DomainEvent $domainEvent): void;

    /**
     * @param int|null $eventId
     * @return array
     */
    public function storedEventsSince(?int $eventId): array;
}
