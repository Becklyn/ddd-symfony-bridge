<?php declare(strict_types=1);

namespace Becklyn\Ddd\Identity\Infrastructure\Delivery\Web\Symfony;

use Becklyn\Ddd\Identity\Domain\AggregateId;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Christoph Hautzinger <ch@becklyn.com>
 *
 * @since  2019-11-15
 */
class AggregateIdParamConverter implements ParamConverterInterface
{
    public function apply(Request $request, ParamConverter $configuration) : bool
    {
        $aggregateId = $request->attributes->get($configuration->getName());
        $class = $configuration->getClass();

        $request->attributes->set($configuration->getName(), $class::fromString($aggregateId));

        return true;
    }

    public function supports(ParamConverter $configuration) : bool
    {
        if (\empty($configuration->getClass()) || !\class_exists($configuration->getClass())) {
            return false;
        }

        return \in_array(AggregateId::class, \class_implements($configuration->getClass()), true);
    }
}
