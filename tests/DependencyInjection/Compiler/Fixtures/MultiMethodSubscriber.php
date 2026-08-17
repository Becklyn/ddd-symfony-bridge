<?php declare(strict_types=1);

namespace Becklyn\Ddd\Tests\DependencyInjection\Compiler\Fixtures;

class MultiMethodSubscriber
{
    public function handle(ExampleEvent $event) : void
    {
    }

    public function handleOther(OtherExampleEvent $event) : void
    {
    }
}
