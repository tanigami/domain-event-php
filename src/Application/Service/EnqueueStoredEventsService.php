<?php

namespace Tanigami\DomainEvent\Application\Service;

use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrProducer;
use Interop\Queue\PsrTopic;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Tanigami\DomainEvent\Domain\Model\EventStore;
use Tanigami\DomainEvent\Domain\Model\FailedToEnqueueStoredEventException;
use Tanigami\DomainEvent\Domain\Model\EnqueuedStoredEventTrackerStore;
use Tanigami\DomainEvent\Domain\Model\StoredEvent;


class EnqueueStoredEventsService
{
    /**
     * @var EventStore
     */
    protected $eventStore;

    /**
     * @var EnqueuedStoredEventTrackerStore
     */
    protected $enqueuedStoredEventTrackerStore;

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
     * @param EnqueuedStoredEventTrackerStore $enqueuedStoredEventTrackerStore
     */
    public function __construct(
        PsrContext $context,
        EventStore $eventStore,
        EnqueuedStoredEventTrackerStore $enqueuedStoredEventTrackerStore
    ) {
        $this->context = $context;
        $this->eventStore = $eventStore;
        $this->enqueuedStoredEventTrackerStore = $enqueuedStoredEventTrackerStore;
    }

    /**
     * @param string $topicName
     * @return int
     * @throws FailedToEnqueueStoredEventException
     */
    public function execute(string $topicName): int
    {
        $enqueuedMessagesCount = 0;
        $lastEnqueuedStoredEvent = null;

        $storedEventsToEnqueue = $this->getStoredEventsToEnqueue($topicName);
        if (0 === count($storedEventsToEnqueue)) {
            return $enqueuedMessagesCount;
        }

        $producer = $this->createProducer();
        $topic = $this->createTopic($topicName);

        try {
            foreach ($storedEventsToEnqueue as $storedEvent) {
                $message = $this->createMessage($storedEvent);
                $producer->send($topic, $message);
                $enqueuedMessagesCount = $enqueuedMessagesCount + 1;
                $lastEnqueuedStoredEvent = $storedEvent;
            }
        } catch (\Interop\Queue\Exception $e) {
            throw new FailedToEnqueueStoredEventException($e);
        } finally {
            if (null !== $lastEnqueuedStoredEvent) {
                $this->enqueuedStoredEventTrackerStore
                    ->trackLastEnqueuedStoredEvent($topicName, $lastEnqueuedStoredEvent);
            }
        }

        return $enqueuedMessagesCount;
    }

    /**
     * @param string $topicName
     * @return StoredEvent[]
     */
    private function getStoredEventsToEnqueue(string $topicName): array
    {
        return $this->eventStore->storedEventsSince(
            $this->enqueuedStoredEventTrackerStore->lastEnqueuedStoredEventId($topicName)
        );
    }

    /**
     * @return PsrProducer
     */
    protected function createProducer(): PsrProducer
    {
        $producer = $this->context->createProducer();

        return $producer;
    }

    /**
     * @param string $topicName
     * @return PsrTopic
     */
    protected function createTopic(string $topicName): PsrTopic
    {
        $topic = $this->context->createTopic($topicName);
        $topic = $this->configureTopic($topic);

        return $topic;
    }

    /**
     * @param PsrTopic $topic
     * @return PsrTopic
     */
    protected function configureTopic(PsrTopic $topic): PsrTopic
    {
        return $topic;
    }

    /**
     * @param StoredEvent $storedEvent
     * @return PsrMessage
     */
    protected function createMessage(StoredEvent $storedEvent): PsrMessage
    {
        $message = $this->context->createMessage($this->serializer()->serialize($storedEvent, 'json'));
        $message = $this->configureMessage($message);

        return $message;
    }

    /**
     * @param PsrMessage $message
     * @return PsrMessage
     */
    protected function configureMessage(PsrMessage $message): PsrMessage
    {
        return $message;
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
                    ->setCacheDir(__DIR__.'/../../../var/cache/jms-serializer')
                    ->build();
        }

        return $this->serializer;
    }
}
