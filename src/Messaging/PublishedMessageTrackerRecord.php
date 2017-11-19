<?php

namespace Tanigami\DomainEven\Messaging;

class PublishedMessageTrackerRecord
{
    /**
     * @var int
     */
    private $trackerId;

    /**
     * @var int
     */
    private $mostRecentPublishedStoredEventId;

    /**
     * @var string
     */
    private $type;

    /**
     * @param string $type
     * @param int $aMostRecentPublishedMessageId
     */
    public function __construct(string $type, int $mostRecentPublishedStoredEventId)
    {
        $this->type = $type;
        $this->mostRecentPublishedStoredEventId = $mostRecentPublishedStoredEventId;
    }

    /**
     * @return int
     */
    public function trackerId(): int
    {
        return $this->trackerId;
    }

    /**
     * @return int
     */
    public function mostRecentPublishedStoredEventId(): int
    {
        return $this->mostRecentPublishedStoredEventId;
    }

    /**
     * @param int $maxId
     */
    public function updateMostRecentPublishedMessageId(int $mostRecentPublishedStoredEventId): void
    {
        $this->mostRecentPublishedStoredEventId = $mostRecentPublishedStoredEventId;
    }
}
