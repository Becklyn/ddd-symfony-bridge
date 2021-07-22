<?php

namespace Becklyn\Ddd\Commands\Infrastructure\SimpleBus;

use Becklyn\Ddd\Commands\Application\CommandBus as CommandBusInterface;
use SimpleBus\SymfonyBridge\Bus\CommandBus;

/**
 * @author Marko Vujnovic <mv@becklyn.com>
 * @since  2019-06-05
 */
class SimpleBusCommandBus implements CommandBusInterface
{
    public function __construct(
        private CommandBus $commandBus,
    ) {
    }

    public function dispatch($command): void
    {
        $this->commandBus->handle($command);
    }
}
