<?php

namespace Tanigami\DomainEvent\Infrastructure\Domain\Model;

use Doctrine\ORM\EntityRepository;
use Tanigami\DomainEvent\Domain\Model\PublishedStoredEventTracker;
use Tanigami\DomainEvent\Domain\Model\PublishedStoredEventTrackerStore;
use Tanigami\DomainEvent\Domain\Model\StoredEvent;

class DoctrinePublishedStoredEventTrackerStore extends EntityRepository implements PublishedStoredEventTrackerStore
{
    /**
     * {@inheritdoc}
     * @throws \Doctrine\ORM\ORMException
     */
    public function trackLastPublishedStoredEvent(string $topic, StoredEvent $storedEvent): void
    {
        $id = $storedEvent->id();
        $publishedStoredEventTracker = $this->findOneByTopic($topic);
        if (null === $publishedStoredEventTracker) {
            $publishedStoredEventTracker = new PublishedStoredEventTracker($topic, $id);
        }
        $publishedStoredEventTracker->updateLastPublishedStoredEventId($id);
        $this->getEntityManager()->persist($publishedStoredEventTracker);
    }

    /**
     * @param string $topic
     * @return int|null
     */
    public function lastPublishedStoredEventId(string $topic): ?int
    {
        /** @var PublishedStoredEventTracker $publishedStoredEventTracker */
        $publishedStoredEventTracker = $this->findOneByTopic($topic);
        if (null === $publishedStoredEventTracker) {
            return null;
        }

        return $publishedStoredEventTracker->lastPublishedStoredEventId();
    }
}