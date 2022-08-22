<?php

namespace Becklyn\Ddd\Tests\Commands\Infrastructure;

use Becklyn\Ddd\Commands\Domain\Command;
use Becklyn\Ddd\Commands\Infrastructure\SimpleBus\SimpleBusCommandBus;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use SimpleBus\SymfonyBridge\Bus\CommandBus;

class SimpleBusCommandBusTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy|CommandBus $commandBus;

    private SimpleBusCommandBus $fixture;

    protected function setUp(): void
    {
        $this->commandBus = $this->prophesize(CommandBus::class);
        $this->fixture = new SimpleBusCommandBus($this->commandBus->reveal());
    }

    public function testDispatchCallsHandleOnBase(): void
    {
        $command = $this->givenACommand();
        $this->whenACommandIsDispatchedThroughSimpleBusCommandBus($command);
        $this->thenTheCommandShouldBeHandledByTheCommandBusFromTheSimpleBusLibrary($command);
    }

    private function givenACommand(): Command
    {
        return $this->prophesize(Command::class)->reveal();
    }

    private function whenACommandIsDispatchedThroughSimpleBusCommandBus(Command $command): void
    {
        $this->fixture->dispatch($command);
    }

    private function thenTheCommandShouldBeHandledByTheCommandBusFromTheSimpleBusLibrary($command): void
    {
        $this->commandBus->handle($command)->shouldBeCalledTimes(1);
    }
}
