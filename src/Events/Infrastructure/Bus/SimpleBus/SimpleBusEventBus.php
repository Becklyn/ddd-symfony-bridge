<?php

namespace Becklyn\Ddd\Events\Infrastructure\Bus\SimpleBus;

use Becklyn\Ddd\Events\Application\EventBus as EventBusInterface;
use Becklyn\Ddd\Events\Domain\DomainEvent;
use SimpleBus\SymfonyBridge\Bus\EventBus;

/**
 * @author Marko Vujnovic <mv@becklyn.com>
 * @since  2019-06-05
 */
class SimpleBusEventBus implements EventBusInterface
{
    private EventBus $eventBus;

    public function __construct(EventBus $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    public function dispatch(DomainEvent $event): void
    {
        $this->eventBus->handle($event);
    }
}
