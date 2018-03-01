<?php

namespace Tanigami\DomainEvent\Infrastructure\Domain\Model;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use PHPUnit\Framework\TestCase;
use Tanigami\DomainEvent\Domain\Model\FakeDomainEvent;
use Tanigami\DomainEvent\Domain\Model\EnqueuedStoredEventTracker;
use Tanigami\DomainEvent\Domain\Model\EnqueuedStoredEventTrackerStore;
use Tanigami\DomainEvent\Domain\Model\StoredEvent;

class DoctrineEnqueuedStoredEventTrackerStoreTest extends TestCase
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EnqueuedStoredEventTrackerStore
     */
    private $enqueuedStoredEventTrackerStore;

    /**
     * @var DoctrineEventStore
     */
    private $eventStore;

    public function setUp()
    {
        $this->entityManager = $this->initEntityManager();
        $this->initSchema($this->entityManager);
        $this->enqueuedStoredEventTrackerStore = $this->createEnqueuedStoredEventTrackerStore();
        $this->eventStore = $this->createEventStore();
    }

    public function testTrackLastEnqueuedStoredEvent()
    {
        $this->eventStore->append(new FakeDomainEvent('EVENT1'));
        $this->eventStore->append(new FakeDomainEvent('EVENT2'));
        $this->eventStore->append(new FakeDomainEvent('EVENT3'));
        $this->entityManager->flush();

        $storedEvents = $this->eventStore->storedEventsSince(null);

        foreach ($storedEvents as $storedEvent) {
            $this->enqueuedStoredEventTrackerStore->trackLastEnqueuedStoredEvent(
                'TOPIC_NAME',
                $storedEvent
            );
            $this->entityManager->flush();
        }

        $id = $this->enqueuedStoredEventTrackerStore->lastEnqueuedStoredEventId('TOPIC_NAME');

        $this->assertSame(3, $id);
    }

    private function createEnqueuedStoredEventTrackerStore()
    {
        return new DoctrineEnqueuedStoredEventTrackerStore(
            $this->entityManager,
            $this->entityManager->getClassMetaData(EnqueuedStoredEventTracker::class)
        );
    }

    /**
     * @return EntityManager
     * @throws \Doctrine\ORM\ORMException
     */
    protected function initEntityManager()
    {
        return EntityManager::create(
            ['url' => 'sqlite:///:memory:'],
            Setup::createXMLMetadataConfiguration(
                [__DIR__],
                $devMode = true
            )
        );
    }

    private function initSchema(EntityManager $entityManager)
    {
        $tool = new SchemaTool($entityManager);
        $tool->createSchema([
            $entityManager->getClassMetadata(StoredEvent::class),
            $entityManager->getClassMetadata(EnqueuedStoredEventTracker::class),
        ]);
    }

    private function createEventStore()
    {
        return new DoctrineEventStore(
            $this->entityManager,
            $this->entityManager->getClassMetaData(StoredEvent::class),
            __DIR__.'/../../../../src/Infrastructure/Serialization/JMS/Config'
        );
    }
}