<?php

declare(strict_types=1);

namespace App\Shared\Domain\Event;

use DateTimeImmutable;

abstract class BaseDomainEvent implements DomainEventInterface
{
    private readonly DateTimeImmutable $occurredOn;

    public function __construct(
        private readonly string $aggregateId
    ) {
        $this->occurredOn = new DateTimeImmutable();
    }

    public function getAggregateId(): string
    {
        return $this->aggregateId;
    }

    public function getOccurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }

    abstract public function getEventName(): string;
}