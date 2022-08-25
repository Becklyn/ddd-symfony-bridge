<?php declare(strict_types=1);

namespace Becklyn\Ddd\Tests\Commands\Infrastructure;

use Becklyn\Ddd\Commands\Domain\Command;
use Becklyn\Ddd\Commands\Infrastructure\SimpleBus\SimpleBusCommandBus;
use Becklyn\Ddd\Messages\Domain\Message;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use SimpleBus\SymfonyBridge\Bus\CommandBus;

class SimpleBusCommandBusTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy|CommandBus $commandBus;

    private SimpleBusCommandBus $fixture;

    protected function setUp() : void
    {
        $this->commandBus = $this->prophesize(CommandBus::class);
        $this->fixture = new SimpleBusCommandBus($this->commandBus->reveal());
    }

    public function testDispatchCallsHandleOnBase() : void
    {
        $command = $this->givenACommand();
        $this->whenACommandIsDispatchedThroughSimpleBusCommandBus($command->reveal());
        $this->thenTheCommandShouldBeHandledByTheCommandBusFromTheSimpleBusLibrary($command->reveal());
    }

    private function givenACommand() : ObjectProphecy|Command
    {
        return $this->prophesize(Command::class);
    }

    private function whenACommandIsDispatchedThroughSimpleBusCommandBus(Command $command, ?Message $correlateWith = null) : void
    {
        $this->fixture->dispatch($command, $correlateWith);
    }

    private function thenTheCommandShouldBeHandledByTheCommandBusFromTheSimpleBusLibrary($command) : void
    {
        $this->commandBus->handle($command)->shouldBeCalledTimes(1);
    }

    public function testCommandIsCorrelatedBeforeBeingHandledByBaseIfCorrelationMessageIsPassed() : void
    {
        $command = $this->givenACommand();
        $correlationMessage = $this->givenACorrelationMessage();

        $this->thenCommandShouldBeCorrelatedBeforeBeingHandledBySimpleBus($command, $correlationMessage);

        $this->whenACommandIsDispatchedThroughSimpleBusCommandBus($command->reveal(), $correlationMessage);
    }

    private function givenACorrelationMessage() : Message
    {
        return $this->prophesize(Message::class)->reveal();
    }

    private function thenCommandShouldBeCorrelatedBeforeBeingHandledBySimpleBus(ObjectProphecy|Command $command, Message $correlationMessage) : void
    {
        $simpleBus = $this->commandBus;

        $command->correlateWith($correlationMessage)->shouldBeCalledTimes(1);
        $command->correlateWith($correlationMessage)->will(function () use ($command, $simpleBus) : void {
            $simpleBus->handle($command->reveal())->shouldBeCalledTimes(1);
        });
    }
}
