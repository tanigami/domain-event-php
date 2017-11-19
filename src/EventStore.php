<?php

namespace Tanigami\DomainEvent;

interface EventStore
{
    /**
     * @param DomainEvent $domainEvent
     * @return mixed
     */
    public function append(DomainEvent $domainEvent): void;

    /**
     * @param int $eventId
     * @return StoredEvent[]
     */
    public function storedEventsSince(int $eventId): array;
}
