<?php declare(strict_types=1);

namespace Becklyn\Ddd\Events\Infrastructure\Bus\Messenger;

use Becklyn\Ddd\Events\Application\EventBus as EventBusInterface;
use Becklyn\Ddd\Events\Domain\DomainEvent;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Symfony Messenger implementation of the event bus. Drop-in replacement for
 * SimpleBusEventBus, which cannot be used beyond Symfony 6 because
 * simple-bus/symfony-bridge caps symfony/* at ^6.0.
 *
 * The bus this is wired to must allow messages without handlers -- domain events
 * with no subscriber are normal and must not raise. See BecklynDddExtension,
 * which prepends allow_no_handlers for the event bus.
 */
class MessengerEventBus implements EventBusInterface
{
    public function __construct(
        private MessageBusInterface $eventBus,
    ) {
    }

    public function dispatch(DomainEvent $event) : void
    {
        $this->eventBus->dispatch($event);
    }
}
