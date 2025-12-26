<?php

declare(strict_types=1);

namespace App\Shared\Domain\Model;

use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

abstract class BaseEntity
{
    protected UuidV4 $id;
    protected DateTimeImmutable $createdAt;
    protected DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    protected function updateTimestamp(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}