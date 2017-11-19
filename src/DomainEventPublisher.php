<?php

namespace Tanigami\DomainEvent;

class DomainEventPublisher
{
    /**
     * @var DomainEventSubscriber[]
     */
    private $subscribers;

    /**
     * @var null|DomainEventPublisher
     */
    private static $instance;

    /**
     * @var int
     */
    private $id = 0;

    /**
     * @return void
     */
    private function __construct()
    {
        $this->subscribers = [];
    }

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
        $id = $this->id;
        $this->subscribers[$id] = $domainEventSubscriber;
        $this->id = $id + 1;

        return $id;
    }

    /**
     * @param int $id
     * @return null|DomainEventSubscriber
     */
    public function ofId(int $id)
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
