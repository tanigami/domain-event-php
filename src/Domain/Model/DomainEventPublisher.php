<?php

namespace Tanigami\DomainEvent\Domain\Model;

/**
 * OK
 */
class DomainEventPublisher
{
    /**
     * @var DomainEventSubscriber[]
     */
    private $subscribers = [];

    /**
     * @var null|self
     */
    protected static $instance = null;

    /**
     * @var int
     */
    private $nextSubscriberId = 1;

    /**
     * @return self
     */
    public static function instance(): self
    {
        if (null === static::$instance) {
            static::$instance = new self;
        }

        return static::$instance;
    }

    /**
     * @param DomainEventSubscriber $domainEventSubscriber
     * @return int
     */
    public function subscribe(DomainEventSubscriber $domainEventSubscriber): int
    {
        $id = $this->nextSubscriberId;
        $this->subscribers[$id] = $domainEventSubscriber;
        $this->nextSubscriberId = $this->nextSubscriberId + 1;

        return $id;
    }

    /**
     * @param int $id
     * @return null|DomainEventSubscriber
     */
    public function subscriberOfId(int $id): ?DomainEventSubscriber
    {
        return $this->subscribers[$id] ?? null;
    }

    /**
     * @param int $id
     */
    public function unsubscribe(int $id): void
    {
        unset($this->subscribers[$id]);
    }

    /**
     * @param DomainEvent $domainEvent
     */
    public function publish(DomainEvent $domainEvent): void
    {
        foreach ($this->subscribers as $subscriber) {
            if ($subscriber->isSubscribedTo($domainEvent)) {
                $subscriber->handle($domainEvent);
            }
        }
    }
}
