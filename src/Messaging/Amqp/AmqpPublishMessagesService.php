<?php

namespace Tanigami\DomainEvent\Amqp;

use JMS\Serializer\Serializer;
use Tanigami\DomainEvent\EventStore;
use Tanigami\DomainEvent\Messaging\PublishedMessageTracker;
use Tanigami\DomainEvent\Messaging\PublishMessagesService;
use Tanigami\DomainEvent\StoredEvent;

class AmqpPublishMessagesService extends PublishMessagesService
{
    /**
     * @var AmqpMessageProducer
     */
    private $messageProducer;

    /**
     * @param EventStore $eventStore
     * @param PublishedMessageTracker $publishedMessageTracker
     * @param AmqpMessageProducer $messageProducer
     */
    public function __construct(
        EventStore $eventStore,
        PublishedMessageTracker $publishedMessageTracker,
        AmqpMessageProducer $messageProducer,
        Serializer $serializer
    ) {
        parent::__construct($eventStore, $publishedMessageTracker, $serializer);
        $this->messageProducer = $messageProducer;
    }

    function publishMessage(StoredEvent $storedEvent, string $exchangeName): void
    {
        $this->messageProducer->send(
            $exchangeName,
            $this->serializer->serialize($storedEvent, 'json'),
            $storedEvent->name(),
            $storedEvent->id(),
            $storedEvent->occurredAt()
        );
    }

    function open(string $exchangeName): void
    {
        // TODO: Implement open() method.
    }

    function close(string $exchangeName): void
    {
        // TODO: Implement close() method.
    }
}
