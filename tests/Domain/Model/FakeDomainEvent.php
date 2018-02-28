<?php

namespace Tanigami\DomainEvent\Domain\Model;

class FakeDomainEvent extends DomainEvent
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct();
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
}