<?php

namespace Tanigami\DomainEvent\Domain\Model;

interface EnqueuedStoredEventTrackerStore
{
    /**
     * @param string $topic
     * @param StoredEvent $storedEvent
     */
    public function trackLastEnqueuedStoredEvent(string $topic, StoredEvent $storedEvent): void;

    /**
     * @param string $topic
     * @return int|null
     */
    public function lastEnqueuedStoredEventId(string $topic): ?int;
}
