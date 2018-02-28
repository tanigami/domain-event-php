<?php

namespace Tanigami\DomainEvent;

use Tanigami\DomainEvent\Domain\Model\DomainEvent;

class SomethingHappened extends DomainEvent
{
    /**
     * @var string
     */
    private $whatHappened;

    /**
     * @param string $whatHappened
     */
    public function __construct(string $whatHappened)
    {
        parent::__construct();
        $this->whatHappened = $whatHappened;
    }
}