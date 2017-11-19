<?php

namespace Tanigami\DomainEvent;

use PHPUnit\Framework\TestCase;

class DomainEventPublisherTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldNotifySubscriber()
    {
        $this->subscribe($subscriber = new SpySubscriber('test-event'));
        $this->publish($domainEvent = new FakeDomainEvent('test-event'));

        $this->assertEventHandled($subscriber, $domainEvent);
    }

    private function subscribe($subscriber)
    {
        return DomainEventPublisher::instance()->subscribe($subscriber);
    }

    private function publish($domainEvent)
    {
        DomainEventPublisher::instance()->publish($domainEvent);
    }

    private function assertEventHandled($subscriber, $domainEvent)
    {
        $this->assertTrue($subscriber->isHandled);
        $this->assertEquals($domainEvent, $subscriber->domainEvent);
    }

    /**
     * @test
     */
    public function notSubscribedSubscribersShouldNotBeNotified()
    {
        $this->subscribe($subscriber = new SpySubscriber('test-event'));
        $this->publish(new FakeDomainEvent('other-test-event'));

        $this->assertEventNotHandled($subscriber);
    }

    private function assertEventNotHandled($subscriber)
    {
        $this->assertFalse($subscriber->isHandled);
        $this->assertNull($subscriber->domainEvent);
    }

    /**
     * @test
     */
    public function itShouldUnsubscribeSubscriber()
    {
        $subscriberId = $this->subscribe($subscriber = new SpySubscriber('test-event'));
        $this->unsubscribe($subscriberId);
        $this->publish(new FakeDomainEvent('test-event'));

        $this->assertEventNotHandled($subscriber);
    }

    private function unsubscribe($id)
    {
        DomainEventPublisher::instance()->unsubscribe($id);
    }
}

class SpySubscriber implements DomainEventSubscriber
{
    public $domainEvent;
    public $isHandled = false;
    private $eventName;

    public function __construct(string $eventName)
    {
        $this->eventName = $eventName;
    }

    public function isSubscribedTo(DomainEvent $aDomainEvent): bool
    {
        return $this->eventName === $aDomainEvent->name;
    }

    public function handle(DomainEvent $aDomainEvent): void
    {
        $this->domainEvent = $aDomainEvent;
        $this->isHandled = true;
    }
}

class FakeDomainEvent extends DomainEvent
{
    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function occurredOn() {}
}
