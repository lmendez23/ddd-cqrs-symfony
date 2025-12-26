<?php

declare(strict_types=1);

namespace App\Shared\Domain\Contracts;

use App\Shared\Domain\Model\BaseEntity;
use Symfony\Component\Uid\UuidV4;

interface RepositoryInterface
{
    public function save(BaseEntity $entity): void;
    
    public function findById(UuidV4 $id): ?BaseEntity;
    
    public function delete(BaseEntity $entity): void;
}