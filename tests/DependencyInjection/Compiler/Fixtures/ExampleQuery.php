<?php declare(strict_types=1);

namespace Becklyn\Ddd\Tests\DependencyInjection\Compiler\Fixtures;

/** Neither a Command nor a DomainEvent -- must never be routed to a bus. */
class ExampleQuery
{
}
