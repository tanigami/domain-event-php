<?php

namespace Tanigami\DomainEvent\Messaging;

use JMS\Serializer\Serializer;
use Tanigami\DomainEven\Messaging\PublishedMessageTrackerRecord;
use Tanigami\DomainEvent\EventStore;
use Tanigami\DomainEvent\StoredEvent;

abstract class PublishMessagesService
{
    /**
     * @var EventStore
     */
    protected $eventStore;

    /**
     * @var PublishedMessageTracker
     */
    protected $publishedMessageTracker;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @param EventStore $eventStore
     * @param PublishedMessageTracker $publishedMessageTracker
     */
    public function __construct(
        EventStore $eventStore,
        PublishedMessageTracker $publishedMessageTracker,
        Serializer $serializer
    ) {
        $this->eventStore = $eventStore;
        $this->publishedMessageTracker = $publishedMessageTracker;
        $this->serializer = $serializer;
    }

    /**
     * @param string $exchangeName
     * @return int
     */
    public function execute(string $exchangeName)
    {
        $storedEvents = $this->eventStore->storedEventsSince(
            $this->publishedMessageTracker->mostRecentPublishedMessageId($exchangeName)
        );
        if (!$storedEvents) {
            return 0;
        }
        $publishedMessagesCount = 0;
        $lastPublishedStoreEvent = null;
        try {
            $this->open($exchangeName);
            foreach ($storedEvents as $storedEvent) {
                $this->publishMessage($storedEvent, $exchangeName);
                $lastPublishedStoreEvent = $storedEvent;
                $publishedMessagesCount = $publishedMessagesCount + 1;
            }
            $this->close($exchangeName);
        } catch(\Exception $e) {
            var_dump($e);
        }

        $publishedMessage = new PublishedMessageTrackerRecord(
            $exchangeName,
            $lastPublishedStoreEvent->id()
        );

        $this->publishedMessageTracker->trackMostRecentPublishedMessage($publishedMessage);

        return $publishedMessagesCount;
    }

    abstract function open(string $exchangeName): void;

    abstract function publishMessage(StoredEvent $storedEvent, string $exchangeName): void;

    abstract function close(string $exchangeName): void;
}
