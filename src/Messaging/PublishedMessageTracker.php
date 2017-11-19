<?php

namespace Tanigami\DomainEvent\Messaging;

use Tanigami\DomainEvent\StoredEvent;

interface PublishedMessageTracker
{
    /**
     * @param string $type
     * @return int|null
     */
    public function mostRecentPublishedMessageId(string $type);

    /**
     * @param string $type
     * @param StoredEvent $storedEvent
     */
    public function trackMostRecentPublishedMessage(PublishedMessageTrackerRecord $publishedMessage): void;
}
