<?php declare(strict_types=1);

namespace Becklyn\Ddd\Commands\Infrastructure\Messenger;

use Becklyn\Ddd\Commands\Application\CommandBus as CommandBusInterface;
use Becklyn\Ddd\Commands\Domain\Command;
use Becklyn\Ddd\Messages\Domain\Message;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Symfony Messenger implementation of the command bus. Drop-in replacement for
 * SimpleBusCommandBus, which cannot be used beyond Symfony 6 because
 * simple-bus/symfony-bridge caps symfony/* at ^6.0.
 */
class MessengerCommandBus implements CommandBusInterface
{
    public function __construct(
        private MessageBusInterface $commandBus,
    ) {
    }

    public function dispatch(Command $command) : void
    {
        $this->commandBus->dispatch($command);
    }

    public function dispatchAndCorrelate(Command $command, Message $correlateWith) : void
    {
        $command->correlateWith($correlateWith);
        $this->commandBus->dispatch($command);
    }
}
