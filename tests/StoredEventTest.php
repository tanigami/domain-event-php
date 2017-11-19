<?php

namespace Tanigami\DomainEvent;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class StoredEventTest extends TestCase
{
    public function testConstructor()
    {
        $occurredAt = new DateTimeImmutable;
        $storedEvent = new StoredEvent('NAME', 'BODY', $occurredAt);
        $this->assertNull($storedEvent->id());
        $this->assertSame('NAME', $storedEvent->name());
        $this->assertSame('BODY', $storedEvent->body());
        $this->assertEquals($occurredAt, $storedEvent->occurredAt());
    }
}
