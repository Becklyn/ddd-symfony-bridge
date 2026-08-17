<?php declare(strict_types=1);

namespace Becklyn\Ddd\Tests\DependencyInjection\Compiler\Fixtures;

use Becklyn\Ddd\Commands\Domain\Command;
use Becklyn\Ddd\Events\Application\EventBus;

class ExampleCommandHandler
{
    public function handle(ExampleCommand $command) : void
    {
    }

    /** Mirrors the inherited abstract template method; typed against the interface. */
    public function executeTemplate(Command $command) : void
    {
    }

    /** A query handler method -- invoked directly, never dispatched. */
    public function execute(ExampleQuery $query) : void
    {
    }

    /** Setter injection, not a handler. */
    public function setEventBus(EventBus $eventBus) : void
    {
    }

    public function noParameters() : void
    {
    }
}
