<?php

namespace Tanigami\DomainEvent\Infrastructure\Domain\Model;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use PHPUnit\Framework\TestCase;
use Tanigami\DomainEvent\Domain\Model\FakeDomainEvent;
use Tanigami\DomainEvent\Domain\Model\StoredEvent;

class DoctrineEventStoreTest extends TestCase
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var DoctrineEventStore
     */
    private $eventStore;

    public function setUp()
    {
        $this->entityManager = $this->initEntityManager();
        $this->initSchema($this->entityManager);
        $this->eventStore = $this->createEventStore();
    }

    public function testAppend()
    {
        $this->eventStore->append(new FakeDomainEvent('fake'));
        $this->entityManager->flush();
        $storedEvents = $this->eventStore->storedEventsSince(null);
        $this->assertCount(1, $storedEvents);
        $this->assertSame(1, $storedEvents[0]->id());
    }

    public function testStoredEventsSince()
    {
        $this->eventStore->append(new FakeDomainEvent('fake'));
        $this->eventStore->append(new FakeDomainEvent('fake'));
        $this->eventStore->append(new FakeDomainEvent('fake'));
        $this->entityManager->flush();
        $storedEvents = $this->eventStore->storedEventsSince(1);
        $this->assertCount(2, $storedEvents);
        $this->assertSame(2, $storedEvents[0]->id());
        $this->assertSame(3, $storedEvents[1]->id());
    }

    private function createEventStore()
    {
        return new DoctrineEventStore(
            $this->entityManager,
            $this->entityManager->getClassMetaData(StoredEvent::class),
            __DIR__.'/../../../../src/Infrastructure/Serialization/JMS/Config'
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
        ]);
    }
}