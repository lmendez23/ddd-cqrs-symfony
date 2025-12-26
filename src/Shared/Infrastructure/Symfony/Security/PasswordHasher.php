<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Security;

use App\Shared\Domain\Contracts\PasswordHasherInterface;
use App\Shared\Domain\Model\HashedPassword;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

final class PasswordHasher implements PasswordHasherInterface
{
    public function __construct(
        private readonly PasswordHasherFactoryInterface $passwordHasherFactory
    ) {
    }

    public function hash(string $plainPassword): HashedPassword
    {
        $hasher = $this->passwordHasherFactory->getPasswordHasher(
            PasswordAuthenticatedUserInterface::class
        );
        
        return new HashedPassword($hasher->hash($plainPassword));
    }

    public function verify(string $plainPassword, HashedPassword $hashedPassword): bool
    {
        $hasher = $this->passwordHasherFactory->getPasswordHasher(
            PasswordAuthenticatedUserInterface::class
        );
        
        return $hasher->verify($hashedPassword->getValue(), $plainPassword);
    }
}