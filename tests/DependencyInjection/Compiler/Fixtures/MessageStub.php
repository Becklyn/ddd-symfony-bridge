<?php declare(strict_types=1);

namespace Becklyn\Ddd\Tests\DependencyInjection\Compiler\Fixtures;

use Becklyn\Ddd\Messages\Domain\Message;
use Becklyn\Ddd\Messages\Domain\MessageId;

/**
 * RegisterHandlersPass works purely off reflection, so the fixtures need real
 * classes with real method signatures rather than prophecies.
 */
trait MessageStub
{
    public function correlationId() : MessageId
    {
        throw new \LogicException('not used');
    }

    public function causationId() : MessageId
    {
        throw new \LogicException('not used');
    }

    public function correlateWith(Message $message) : void
    {
        throw new \LogicException('not used');
    }
}
