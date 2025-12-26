<?php

declare(strict_types=1);

namespace App\Shared\Domain\Contracts;

interface QueryBusInterface
{
    public function ask(object $query): mixed;
}