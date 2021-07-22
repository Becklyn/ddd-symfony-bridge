<?php

namespace Becklyn\Ddd\Tests\Events\Infrastructure\Bus\SimpleBus;

use Becklyn\Ddd\Events\Domain\DomainEvent;
use Becklyn\Ddd\Events\Infrastructure\Bus\SimpleBus\SimpleBusEventBus;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use SimpleBus\SymfonyBridge\Bus\EventBus;

class SimpleBusEventBusTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy|EventBus */
    private ObjectProphecy $eventBus;

    private SimpleBusEventBus $fixture;

    protected function setUp(): void
    {
        $this->eventBus = $this->prophesize(EventBus::class);
        $this->fixture = new SimpleBusEventBus($this->eventBus->reveal());
    }

    public function testDispatchPassesEventToSimpleBus(): void
    {
        $event = $this->givenADomainEvent();
        $this->whenADomainEventIsDispatchedThroughSimpleBusEventBus($event);
        $this->thenTheDomainEventShouldBeHandledByTheEventBusFromTheSimpleBusLibrary($event);
    }

    private function givenADomainEvent(): DomainEvent
    {
        return $this->prophesize(DomainEvent::class)->reveal();
    }

    private function whenADomainEventIsDispatchedThroughSimpleBusEventBus(DomainEvent $event): void
    {
        $this->fixture->dispatch($event);
    }

    private function thenTheDomainEventShouldBeHandledByTheEventBusFromTheSimpleBusLibrary(DomainEvent $event): void
    {
        $this->eventBus->handle($event)->shouldBeCalledTimes(1);
    }
}
