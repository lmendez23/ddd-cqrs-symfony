<?php

declare(strict_types=1);

namespace App\Shared\Domain\Contracts;

use App\Shared\Domain\Model\HashedPassword;

interface PasswordHasherInterface
{
    public function hash(string $plainPassword): HashedPassword;
    
    public function verify(string $plainPassword, HashedPassword $hashedPassword): bool;
}