<?php

namespace Tanigami\DomainEvent\Infrastructure\Domain\Model;

use Doctrine\ORM\EntityRepository;
use Tanigami\DomainEvent\Domain\Model\EnqueuedStoredEventTracker;
use Tanigami\DomainEvent\Domain\Model\EnqueuedStoredEventTrackerStore;
use Tanigami\DomainEvent\Domain\Model\StoredEvent;

class DoctrineEnqueuedStoredEventTrackerStore extends EntityRepository implements EnqueuedStoredEventTrackerStore
{
    /**
     * {@inheritdoc}
     * @throws \Doctrine\ORM\ORMException
     */
    public function trackLastEnqueuedStoredEvent(string $topic, StoredEvent $storedEvent): void
    {
        $id = $storedEvent->id();
        $publishedStoredEventTracker = $this->findOneByTopic($topic);
        if (null === $publishedStoredEventTracker) {
            $publishedStoredEventTracker = new EnqueuedStoredEventTracker($topic, $id);
        }
        $publishedStoredEventTracker->updateLastEnqueuedStoredEventId($id);
        $this->getEntityManager()->persist($publishedStoredEventTracker);
    }

    /**
     * @param string $topic
     * @return int|null
     */
    public function lastEnqueuedStoredEventId(string $topic): ?int
    {
        /** @var EnqueuedStoredEventTracker $publishedStoredEventTracker */
        $publishedStoredEventTracker = $this->findOneByTopic($topic);
        if (null === $publishedStoredEventTracker) {
            return null;
        }

        return $publishedStoredEventTracker->lastEnqueuedStoredEventId();
    }
}