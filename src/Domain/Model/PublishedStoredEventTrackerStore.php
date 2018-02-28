<?php

namespace Tanigami\DomainEvent\Domain\Model;

interface PublishedStoredEventTrackerStore
{
    /**
     * @param string $topic
     * @param StoredEvent $storedEvent
     */
    public function trackLastPublishedStoredEvent(string $topic, StoredEvent $storedEvent): void;

    /**
     * @param string $topic
     * @return int|null
     */
    public function lastPublishedStoredEventId(string $topic): ?int;
}
