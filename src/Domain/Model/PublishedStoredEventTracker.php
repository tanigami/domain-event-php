<?php

namespace Tanigami\DomainEvent\Domain\Model;

class PublishedStoredEventTracker
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
    private $lastPublishedStoredEventId;

    /**
     * @param string $topic
     * @param int $lastPublishedStoredEventId
     */
    public function __construct(string $topic, int $lastPublishedStoredEventId)
    {
        $this->topic = $topic;
        $this->lastPublishedStoredEventId = $lastPublishedStoredEventId;
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
    public function lastPublishedStoredEventId(): int
    {
        return $this->lastPublishedStoredEventId;
    }

    /**
     * @param int $lastPublishedStoredEventId
     */
    public function updateLastPublishedStoredEventId(int $lastPublishedStoredEventId): void
    {
        $this->lastPublishedStoredEventId = $lastPublishedStoredEventId;
    }
}
