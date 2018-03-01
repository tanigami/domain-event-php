<?php

namespace Tanigami\DomainEvent\Application\Service;

use DateTimeImmutable;
use Enqueue\Null\NullContext;
use Enqueue\Null\NullProducer;
use Enqueue\Null\NullTopic;
use Interop\Queue\InvalidMessageException;
use Interop\Queue\PsrDestination;
use Interop\Queue\PsrMessage;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tanigami\DomainEvent\Domain\Model\EventStore;
use Tanigami\DomainEvent\Domain\Model\EnqueuedStoredEventTrackerStore;
use Tanigami\DomainEvent\Domain\Model\StoredEvent;

class EnqueueStoredEventsServiceTest extends TestCase
{
    public function testItReturnsZeroIfNoStoredEventsToEnqueue()
    {
        $context = new NullContext;
        $eventStore = Mockery::mock(EventStore::class);
        $eventStore->shouldReceive(['storedEventsSince' => []]);
        $enqueuedStoredEventTrackerStore = Mockery::mock(EnqueuedStoredEventTrackerStore::class);
        $enqueuedStoredEventTrackerStore->shouldReceive(['lastEnqueuedStoredEventId' => 0]);

        $service = new EnqueueStoredEventsService($context, $eventStore, $enqueuedStoredEventTrackerStore);

        $this->assertSame(0, $service->execute('topic_name'));
    }

    public function testEnqueueThreeStoredEvents()
    {
        $storedEvents = [
            new StoredEvent('event1', 'event1body', new DateTimeImmutable()),
            new StoredEvent('EVENT2', 'event2body', new DateTimeImmutable()),
        ];

        $context = new NullContext();

        $eventStore = Mockery::mock(EventStore::class);
        $eventStore->shouldReceive([
            'storedEventsSince' => $storedEvents,
        ]);
        $enqueuedMessageTrackerStore = Mockery::mock(EnqueuedStoredEventTrackerStore::class);
        $enqueuedMessageTrackerStore
            ->shouldReceive(['lastEnqueuedStoredEventId' => 0])
            ->shouldReceive('trackLastEnqueuedStoredEvent')
            ->once()
            ->withArgs([
                Mockery::on(function (string $topic) {
                    return 'TOPIC_NAME' === $topic;
                }),
                Mockery::on(function (StoredEvent $storedEvent) {
                    return 'EVENT2' === $storedEvent->name();
                })
            ]);

        $service = new EnqueueStoredEventsService($context, $eventStore, $enqueuedMessageTrackerStore);

        $this->assertSame(2, $service->execute('TOPIC_NAME'));
    }

    /**
     * @expectedException  \Tanigami\DomainEvent\Domain\Model\FailedToEnqueueStoredEventException
     */
    public function testIfFailedToEnqueueFirstStoredEvent()
    {
        $storedEvents = [
            new StoredEvent('EVENT1', 'event1body', new DateTimeImmutable()),
            new StoredEvent('EVENT2', 'event2body', new DateTimeImmutable()),
        ];

        $context = new ContextThatCreatesProducerThatFailsToSendSecondMessage();

        $eventStore = Mockery::mock(EventStore::class);
        $eventStore->shouldReceive([
            'storedEventsSince' => $storedEvents,
        ]);
        $enqueuedStoredEventTrackerStore = Mockery::mock(EnqueuedStoredEventTrackerStore::class);
        $enqueuedStoredEventTrackerStore
            ->shouldReceive(['lastEnqueuedStoredEventId' => 0])
            ->shouldReceive('trackLastEnqueuedStoredEvent')
            ->once()
            ->withArgs([
                Mockery::on(function (string $topic) {
                    return 'TOPIC_NAME' === $topic;
                }),
                Mockery::on(function (StoredEvent $storedEvent) {
                    return 'EVENT1' === $storedEvent->name();
                })
            ]);

        $service = new EnqueueStoredEventsService($context, $eventStore, $enqueuedStoredEventTrackerStore);
        $service->execute('TOPIC_NAME');
    }
}

class ContextThatCreatesProducerThatFailsToSendSecondMessage extends NullContext
{
    public function createProducer()
    {
        return new ProducerThatFailsToSendSecondMessage;
    }
}

class ProducerThatFailsToSendSecondMessage extends NullProducer
{
    public function send(PsrDestination $destination, PsrMessage $message)
    {
        if ('EVENT2' === json_decode($message->getBody())->name) {
            throw new InvalidMessageException();
        }
        parent::send($destination, $message);
    }
}