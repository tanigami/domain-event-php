<?php

namespace Tanigami\DomainEvent\Application\Service;

use Exception;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrTopic;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Tanigami\DomainEvent\Domain\Model\EventStore;
use Tanigami\DomainEvent\Domain\Model\FailedToEnqueueStoredEventException;
use Tanigami\DomainEvent\Domain\Model\PublishedStoredEventTrackerStore;
use Tanigami\DomainEvent\Domain\Model\StoredEvent;


class EnqueueStoredEventsService
{
    /**
     * @var EventStore
     */
    protected $eventStore;

    /**
     * @var PublishedStoredEventTrackerStore
     */
    protected $publishedStoredEventTrackerStore;

    /**
     * @var PsrContext
     */
    private $context;

    /**
     * @var Serializer
     */
    private $serializer = null;

    /**
     * @param PsrContext $context
     * @param EventStore $eventStore
     * @param PublishedStoredEventTrackerStore $publishedStoredEventTrackerStore
     */
    public function __construct(
        PsrContext $context,
        EventStore $eventStore,
        PublishedStoredEventTrackerStore $publishedStoredEventTrackerStore
    ) {
        $this->context = $context;
        $this->eventStore = $eventStore;
        $this->publishedStoredEventTrackerStore = $publishedStoredEventTrackerStore;
    }

    /**
     * @param PsrTopic $topic
     * @return int
     * @throws FailedToEnqueueStoredEventException
     */
    public function execute(PsrTopic $topic): int
    {
        $publishedMessagesCount = 0;
        $lastPublishedStoredEvent = null;

        $storedEvents = $this->eventStore->storedEventsSince(
            $this->publishedStoredEventTrackerStore->lastPublishedStoredEventId($topic->getTopicName())
        );
        if (0 === count($storedEvents)) {
            return $publishedMessagesCount;
        }

        try {
            foreach ($storedEvents as $storedEvent) {
                $this->context->createProducer()->send($topic, $this->createMessage($storedEvent));
                $publishedMessagesCount = $publishedMessagesCount + 1;
                $lastPublishedStoredEvent = $storedEvent;
            }
        } catch (Exception $e) {
            throw new FailedToEnqueueStoredEventException($e);
        } finally {
            if (null !== $lastPublishedStoredEvent) {
                $this->publishedStoredEventTrackerStore
                    ->trackLastPublishedStoredEvent($topic->getTopicName(), $lastPublishedStoredEvent);
            }
        }

        return $publishedMessagesCount;
    }

    /**
     * @param StoredEvent $storedEvent
     * @return PsrMessage
     */
    protected function createMessage(StoredEvent $storedEvent): PsrMessage
    {
        return  $this->context->createMessage($this->serializer()->serialize($storedEvent, 'json'));
    }

    /**
     * @return Serializer
     */
    private function serializer(): Serializer
    {
        if (null === $this->serializer) {
            $this->serializer =
                SerializerBuilder::create()
                    ->addMetadataDir(__DIR__.'/../../Infrastructure/Serialization/JMS/Config')
                    ->setCacheDir(__DIR__ . '/../../../var/cache/jms-serializer')
                    ->build();
        }

        return $this->serializer;
    }
}
