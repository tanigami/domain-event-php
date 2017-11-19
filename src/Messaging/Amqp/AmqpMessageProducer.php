<?php

namespace Tanigami\DomainEvent\Messaging\Amqp;

use DateTimeImmutable;

interface AmqpMessageProducer
{
    /**
     * @param string $exchangeName
     */
    public function open(string $exchangeName): void;

    /**
     * @param string $exchangeName
     * @param string $body
     * @param string $type
     * @param string $id
     * @param DateTimeImmutable $occurredAt
     */
    public function send(
        string $exchangeName,
        string $body,
        string $type,
        string $id,
        DateTimeImmutable $occurredAt
    ): void;

    /**
     * @param string $exchangeName
     */
    public function close(string $exchangeName): void;
}
