<?php

namespace C201\Ddd\Tests\Commands\Infrastructure;

use C201\Ddd\Commands\Infrastructure\SimpleBus\SimpleBusCommandBus;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use SimpleBus\SymfonyBridge\Bus\CommandBus;

class SimpleBusCommandBusTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy|CommandBus */
    private ObjectProphecy $commandBus;

    private SimpleBusCommandBus $fixture;

    protected function setUp(): void
    {
        $this->commandBus = $this->prophesize(CommandBus::class);
        $this->fixture = new SimpleBusCommandBus($this->commandBus->reveal());
    }

    public function testDispatchCallsHandleOnBase()
    {
        $command = $this->givenACommand();
        $this->whenACommandIsDispatchedThroughSimpleBusCommandBus($command);
        $this->thenTheCommandShouldBeHandledByTheCommandBusFromTheSimpleBusLibrary($command);
    }

    private function givenACommand()
    {
        return new \stdClass();
    }

    private function whenACommandIsDispatchedThroughSimpleBusCommandBus(\stdClass $command): void
    {
        $this->fixture->dispatch($command);
    }

    private function thenTheCommandShouldBeHandledByTheCommandBusFromTheSimpleBusLibrary($command): void
    {
        $this->commandBus->handle($command)->shouldBeCalledTimes(1);
    }
}
