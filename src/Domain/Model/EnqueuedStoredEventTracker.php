<?php

namespace Tanigami\DomainEvent\Domain\Model;

class EnqueuedStoredEventTracker
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $topic;

    /**
     * @var int
     */
    private $lastEnqueuedStoredEventId;

    /**
     * @param string $topic
     * @param int $lastEnqueuedStoredEventId
     */
    public function __construct(string $topic, int $lastEnqueuedStoredEventId)
    {
        $this->topic = $topic;
        $this->lastEnqueuedStoredEventId = $lastEnqueuedStoredEventId;
    }

    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function topic(): string
    {
        return $this->topic;
    }

    /**
     * @return int
     */
    public function lastEnqueuedStoredEventId(): int
    {
        return $this->lastEnqueuedStoredEventId;
    }

    /**
     * @param int $lastEnqueuedStoredEventId
     */
    public function updateLastEnqueuedStoredEventId(int $lastEnqueuedStoredEventId): void
    {
        $this->lastEnqueuedStoredEventId = $lastEnqueuedStoredEventId;
    }
}
