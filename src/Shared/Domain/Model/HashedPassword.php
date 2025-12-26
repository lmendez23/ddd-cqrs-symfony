<?php

declare(strict_types=1);

namespace App\Shared\Domain\Model;

use InvalidArgumentException;

final readonly class HashedPassword
{
    public function __construct(
        private string $value
    ) {
        if (empty($value)) {
            throw new InvalidArgumentException('Hashed password cannot be empty');
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(HashedPassword $other): bool
    {
        return $this->value === $other->value;
    }
}