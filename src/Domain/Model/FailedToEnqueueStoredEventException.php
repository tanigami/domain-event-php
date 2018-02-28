<?php

namespace Tanigami\DomainEvent\Domain\Model;

class FailedToEnqueueStoredEventException extends Exception
{
    public function __construct(\Exception $previous)
    {
        parent::__construct('Failed to enqueue.', 0, $previous);
    }
}