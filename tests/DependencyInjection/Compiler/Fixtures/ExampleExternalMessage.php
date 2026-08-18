<?php declare(strict_types=1);

namespace Becklyn\Ddd\Tests\DependencyInjection\Compiler\Fixtures;

use Becklyn\Ddd\Messages\Domain\Message;
use Becklyn\Ddd\Messages\Domain\MessageId;

/**
 * Represents an inbound message that implements Message directly rather than
 * the narrower DomainEvent -- e.g. an external event arriving from another
 * service, which has no aggregate to speak of. A subscriber handling this
 * must still be registered on the event bus.
 */
class ExampleExternalMessage implements Message
{
    use MessageStub;

    public function id() : MessageId
    {
        throw new \LogicException('not used');
    }
}
