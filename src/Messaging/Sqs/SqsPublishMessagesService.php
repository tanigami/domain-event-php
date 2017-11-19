<?php

namespace Tanigami\DomainEvent\Messaging\Sqs;

use Aws\Sqs\SqsClient;
use JMS\Serializer\Serializer;
use Tanigami\DomainEvent\EventStore;
use Tanigami\DomainEvent\Messaging\PublishedMessageTracker;
use Tanigami\DomainEvent\Messaging\PublishMessagesService;
use Tanigami\DomainEvent\StoredEvent;

class SqsPublishMessagesService extends PublishMessagesService
{
    /**
     * @var SqsClient
     */
    private $sqsClient;

    /**
     * @param EventStore $eventStore
     * @param PublishedMessageTracker $publishedMessageTracker
     * @param SqsClient $sqsClient
     * @param Serializer $serializer
     */
    public function __construct(
        EventStore $eventStore,
        PublishedMessageTracker $publishedMessageTracker,
        SqsClient $sqsClient,
        Serializer $serializer
    ) {
        parent::__construct($eventStore, $publishedMessageTracker, $serializer);
        $this->sqsClient = $sqsClient;
    }

    function open(string $exchangeName): void
    {
    }

    function publishMessage(StoredEvent $storedEvent, string $exchangeName): void
    {
        $this->sqsClient->sendMessage([
            'MessageAttributes' => [
                "Type" => [
                    'DataType' => "String",
                    'StringValue' => $storedEvent->name(),
                ],
            ],
            'MessageBody' => $storedEvent->body(),
            'QueueUrl' => $exchangeName
        ]);
    }

    function close(string $exchangeName): void
    {
    }
}
