<?php

namespace Tanigami\DomainEvent;

interface DomainEventSubscriber
{
    /**
     * @param DomainEvent $domainEvent
     * @return bool
     */
    public function isSubscribedTo(DomainEvent $domainEvent): bool;

    /**
     * @param DomainEvent $domainEvent
     */
    public function handle(DomainEvent $domainEvent): void;
}
