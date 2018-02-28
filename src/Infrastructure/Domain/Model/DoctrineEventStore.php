<?php

namespace Tanigami\DomainEvent\Infrastructure\Domain\Model;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Tanigami\DomainEvent\Domain\Model\DomainEvent;
use Tanigami\DomainEvent\Domain\Model\EventStore;
use Tanigami\DomainEvent\Domain\Model\StoredEvent;

class DoctrineEventStore extends EntityRepository implements EventStore
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var string
     */
    private $serializerConfigDir;

    /**
     * @param EntityManager $entityManager
     * @param ClassMetadata $classMetadata
     * @param string $serializerConfigDir
     */
    public function __construct(EntityManager $entityManager, ClassMetadata $classMetadata, string $serializerConfigDir)
    {
        parent::__construct($entityManager, $classMetadata);
        $this->serializerConfigDir = $serializerConfigDir;
    }

    /**
     * {@inheritdoc}
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function append(DomainEvent $domainEvent): void
    {
        $storedEvent = new StoredEvent(
            get_class($domainEvent),
            $this->serializer()->serialize($domainEvent, 'json'),
            $domainEvent->occurredAt()
        );
        $this->getEntityManager()->persist($storedEvent);
    }

    /**
     * {@inheritdoc}
     */
    public function storedEventsSince(?int $eventId): array
    {
        $query = $this->createQueryBuilder('e');
        if (null !== $eventId) {
            $query->where('e.id > :id');
            $query->setParameters(['id' => $eventId]);
        }
        $query->orderBy('e.id');

        return $query->getQuery()->getResult();
    }

    /**
     * @return Serializer
     */
    private function serializer(): Serializer
    {
        if (null === $this->serializer) {
            $this->serializer =
                SerializerBuilder::create()
                    ->addMetadataDir($this->serializerConfigDir)
                    ->setCacheDir(__DIR__.'/../../../../var/cache/jms-serializer')
                    ->build();
        }

        return $this->serializer;
    }
}