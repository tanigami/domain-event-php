<?php

namespace Tanigami\DomainEvent\Domain\Model;

class SpySubscriber implements DomainEventSubscriber
{
    public $domainEvent;
    public $isHandled = false;
    private $eventName;

    public function __construct(string $eventName)
    {
        $this->eventName = $eventName;
    }

    public function isSubscribedTo(DomainEvent $domainEvent): bool
    {
        return $this->eventName === $domainEvent->name();
    }

    public function handle(DomainEvent $domainEvent): void
    {
        $this->domainEvent = $domainEvent;
        $this->isHandled = true;
    }
}