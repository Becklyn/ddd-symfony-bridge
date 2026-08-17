<?php declare(strict_types=1);

namespace Becklyn\Ddd\Tests\Events\Infrastructure\Bus\Messenger;

use Becklyn\Ddd\Events\Domain\DomainEvent;
use Becklyn\Ddd\Events\Infrastructure\Bus\Messenger\MessengerEventBus;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class MessengerEventBusTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy|MessageBusInterface $eventBus;

    private MessengerEventBus $fixture;

    protected function setUp() : void
    {
        $this->eventBus = $this->prophesize(MessageBusInterface::class);
        $this->fixture = new MessengerEventBus($this->eventBus->reveal());
    }

    public function testDispatchPassesEventToMessenger() : void
    {
        $event = $this->prophesize(DomainEvent::class);
        $this->eventBus->dispatch($event->reveal())->willReturn(new Envelope($event->reveal()));

        $this->fixture->dispatch($event->reveal());

        $this->eventBus->dispatch($event->reveal())->shouldBeCalledTimes(1);
    }
}
