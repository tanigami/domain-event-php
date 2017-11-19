<?php

namespace Tanigami\DomainEvent;

class PersistDomainEventSubscriber implements DomainEventSubscriber
{
    /**
     * @var EventStore
     */
    private $eventStore;

    /**
     * @param EventStore $eventStore
     */
    public function __construct(EventStore $eventStore)
    {
        $this->eventStore = $eventStore;
    }

    /**
     * {@inheritdoc}
     */
    public function isSubscribedTo(DomainEvent $domainEvent): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DomainEvent $domainEvent): void
    {
        $this->eventStore->append($domainEvent);
    }
}
