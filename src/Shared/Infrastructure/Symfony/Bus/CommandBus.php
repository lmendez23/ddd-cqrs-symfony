<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Bus;

use App\Shared\Domain\Contracts\CommandBusInterface;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class CommandBus implements CommandBusInterface
{
    use HandleTrait;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->messageBus = $commandBus;
    }

    public function dispatch(object $command): mixed
    {
        return $this->handle($command);
    }
}