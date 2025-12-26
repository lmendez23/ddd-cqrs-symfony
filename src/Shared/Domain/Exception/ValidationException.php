<?php

declare(strict_types=1);

namespace App\Shared\Domain\Exception;

final class ValidationException extends DomainException
{
    private array $errors;

    public function __construct(array $errors, string $message = 'Validation failed')
    {
        $this->errors = $errors;
        parent::__construct($message);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public static function withErrors(array $errors): self
    {
        return new self($errors);
    }
}