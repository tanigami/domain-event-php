<?php

namespace Tanigami\DomainEvent\Domain\Model;

/**
 * OK
 */
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
