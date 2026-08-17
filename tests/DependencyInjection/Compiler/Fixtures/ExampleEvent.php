<?php declare(strict_types=1);

namespace Becklyn\Ddd\Tests\DependencyInjection\Compiler\Fixtures;

use Becklyn\Ddd\Events\Domain\DomainEvent;
use Becklyn\Ddd\Events\Domain\EventId;
use Becklyn\Ddd\Identity\Domain\AggregateId;

class ExampleEvent implements DomainEvent
{
    use MessageStub;

    public function id() : EventId
    {
        throw new \LogicException('not used');
    }

    public function raisedTs() : \DateTimeImmutable
    {
        throw new \LogicException('not used');
    }

    public function aggregateId() : AggregateId
    {
        throw new \LogicException('not used');
    }

    public function aggregateType() : string
    {
        throw new \LogicException('not used');
    }
}
