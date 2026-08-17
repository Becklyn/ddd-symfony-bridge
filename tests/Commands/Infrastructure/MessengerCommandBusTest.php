<?php declare(strict_types=1);

namespace Becklyn\Ddd\Tests\Commands\Infrastructure;

use Becklyn\Ddd\Commands\Domain\Command;
use Becklyn\Ddd\Commands\Infrastructure\Messenger\MessengerCommandBus;
use Becklyn\Ddd\Messages\Domain\Message;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class MessengerCommandBusTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy|MessageBusInterface $commandBus;

    private MessengerCommandBus $fixture;

    protected function setUp() : void
    {
        $this->commandBus = $this->prophesize(MessageBusInterface::class);
        $this->fixture = new MessengerCommandBus($this->commandBus->reveal());
    }

    public function testDispatchPassesCommandToMessenger() : void
    {
        $command = $this->givenACommand();
        $this->commandBus->dispatch($command->reveal())->willReturn(new Envelope($command->reveal()));

        $this->fixture->dispatch($command->reveal());

        $this->commandBus->dispatch($command->reveal())->shouldBeCalledTimes(1);
    }

    public function testDispatchAndCorrelateCorrelatesCommandBeforePassingItToMessenger() : void
    {
        $command = $this->givenACommand();
        $correlationMessage = $this->givenACorrelationMessage();
        $messenger = $this->commandBus;

        $messenger->dispatch($command->reveal())->willReturn(new Envelope($command->reveal()));

        $command->correlateWith($correlationMessage)->shouldBeCalledTimes(1);
        $command->correlateWith($correlationMessage)->will(function () use ($command, $messenger) : void {
            $messenger->dispatch($command->reveal())->shouldBeCalledTimes(1);
        });

        $this->fixture->dispatchAndCorrelate($command->reveal(), $correlationMessage);
    }

    private function givenACommand() : ObjectProphecy|Command
    {
        return $this->prophesize(Command::class);
    }

    private function givenACorrelationMessage() : Message
    {
        return $this->prophesize(Message::class)->reveal();
    }
}
