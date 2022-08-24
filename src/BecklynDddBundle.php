<?php declare(strict_types=1);

namespace Becklyn\Ddd;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Marko Vujnovic <mv@becklyn.com>
 *
 * @since  2020-04-22
 */
class BecklynDddBundle extends Bundle
{
    public function build(ContainerBuilder $container) : void
    {
        parent::build($container);

        $mappings = [
            \realpath(__DIR__ . '/../../ddd-doctrine-bridge/resources/config/doctrine-mapping') => 'Becklyn\\Ddd\\Events\\Infrastructure\\Store\\Doctrine',
        ];

        $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings));
    }
}
