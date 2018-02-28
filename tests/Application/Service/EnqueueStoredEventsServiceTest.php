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
use Tanigami\DomainEvent\Domain\Model\PublishedStoredEventTrackerStore;
use Tanigami\DomainEvent\Domain\Model\StoredEvent;

class EnqueueStoredEventsServiceTest extends TestCase
{
    public function testItReturnsZeroIfNoUnpublishedStoredEventsFound()
    {
        $context = new NullContext;
        $eventStore = Mockery::mock(EventStore::class);
        $eventStore->shouldReceive(['storedEventsSince' => []]);
        $publishedMessageTrackerStore = Mockery::mock(PublishedStoredEventTrackerStore::class);
        $publishedMessageTrackerStore->shouldReceive(['lastPublishedStoredEventId' => 0]);
        $topic = new NullTopic('');

        $service = new EnqueueStoredEventsService($context, $eventStore, $publishedMessageTrackerStore);

        $this->assertSame(0, $service->execute($topic));
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
        $publishedMessageTrackerStore = Mockery::mock(PublishedStoredEventTrackerStore::class);
        $publishedMessageTrackerStore
            ->shouldReceive(['lastPublishedStoredEventId' => 0])
            ->shouldReceive('trackLastPublishedStoredEvent')
            ->once()
            ->withArgs([
                Mockery::on(function (string $topic) {
                    return 'NOTHING' === $topic;
                }),
                Mockery::on(function (StoredEvent $storedEvent) {
                    return 'EVENT2' === $storedEvent->name();
                })
            ]);
        $topic = new NullTopic('NOTHING');

        $service = new EnqueueStoredEventsService($context, $eventStore, $publishedMessageTrackerStore);

        $this->assertSame(2, $service->execute($topic));
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
        $publishedMessageTrackerStore = Mockery::mock(PublishedStoredEventTrackerStore::class);
        $publishedMessageTrackerStore
            ->shouldReceive(['lastPublishedStoredEventId' => 0])
            ->shouldReceive('trackLastPublishedStoredEvent')
            ->once()
            ->withArgs([
                Mockery::on(function (string $topic) {
                    return 'NOTHING' === $topic;
                }),
                Mockery::on(function (StoredEvent $storedEvent) {
                    return 'EVENT1' === $storedEvent->name();
                })
            ]);
        $topic = new NullTopic('NOTHING');

        $service = new EnqueueStoredEventsService($context, $eventStore, $publishedMessageTrackerStore);
        $service->execute($topic);
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