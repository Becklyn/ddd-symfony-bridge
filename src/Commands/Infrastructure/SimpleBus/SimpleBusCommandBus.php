<?php declare(strict_types=1);

namespace Becklyn\Ddd\Commands\Infrastructure\SimpleBus;

use Becklyn\Ddd\Commands\Application\CommandBus as CommandBusInterface;
use Becklyn\Ddd\Commands\Domain\Command;
use Becklyn\Ddd\Messages\Domain\Message;
use SimpleBus\SymfonyBridge\Bus\CommandBus;

/**
 * @author Marko Vujnovic <mv@becklyn.com>
 *
 * @since  2019-06-05
 */
class SimpleBusCommandBus implements CommandBusInterface
{
    public function __construct(
        private CommandBus $commandBus,
    ) {
    }

    public function dispatch(Command $command, ?Message $correlateWith = null) : void
    {
        if ($correlateWith) {
            $command->correlateWith($correlateWith);
        }

        $this->commandBus->handle($command);
    }
}
