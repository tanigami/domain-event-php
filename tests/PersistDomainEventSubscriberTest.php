<?php

namespace Tanigami\DomainEvent;

use Mockery;
use PHPUnit\Framework\TestCase;

class PersistDomainEventSubscriberTest extends TestCase
{
    public function testIsSubscribedToReturnsTrue()
    {
        $eventStore = Mockery::mock(EventStore::class);
        $domainEvent = Mockery::mock(DomainEvent::class);
        $persistDomainEventSubscriber = new PersistDomainEventSubscriber($eventStore);
        $this->assertTrue($persistDomainEventSubscriber->isSubscribedTo($domainEvent));
    }

    public function testHandle()
    {
        $domainEvent = Mockery::mock(DomainEvent::class);
        $eventStore = Mockery::mock(EventStore::class);
        $eventStore->shouldReceive('append')->once()->with($domainEvent);
        $persistDomainEventSubscriber = new PersistDomainEventSubscriber($eventStore);
        $persistDomainEventSubscriber->handle($domainEvent);
        $this->assertTrue(true);
    }
}
