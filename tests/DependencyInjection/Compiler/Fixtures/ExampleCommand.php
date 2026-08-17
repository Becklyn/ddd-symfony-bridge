<?php declare(strict_types=1);

namespace Becklyn\Ddd\Tests\DependencyInjection\Compiler\Fixtures;

use Becklyn\Ddd\Commands\Domain\Command;
use Becklyn\Ddd\Commands\Domain\CommandId;

class ExampleCommand implements Command
{
    use MessageStub;

    public function id() : CommandId
    {
        throw new \LogicException('not used');
    }
}
