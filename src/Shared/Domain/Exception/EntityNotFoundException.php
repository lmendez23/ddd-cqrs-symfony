<?php

declare(strict_types=1);

namespace App\Shared\Domain\Exception;

final class EntityNotFoundException extends DomainException
{
    public static function withId(string $entityType, string $id): self
    {
        return new self(sprintf('%s with ID %s not found', $entityType, $id));
    }
}