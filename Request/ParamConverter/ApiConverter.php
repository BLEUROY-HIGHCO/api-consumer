<?php

namespace Highco\ApiConsumerBundle\Request\ParamConverter;

use Highco\ApiConsumerBundle\Exception\InvalidArgumentException;
use Highco\ApiConsumerBundle\Manager\ManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiConverter implements ParamConverterInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function apply(Request $request, ParamConverter $configuration)
    {
        $name    = $configuration->getName();
        $class   = $configuration->getClass();
        $options = $configuration->getOptions();
        $object  = null;

        if (false !== ($id = $this->getIdentifier($request, $options, $name))) {
            try {
                $object = $this->container->get($options['manager'])->getRepository($class)->getById($id);
            } catch (NotFoundHttpException $e) {
                $object = null;
            }
        }

        if (null === $object && false === $configuration->isOptional()) {
            throw new NotFoundHttpException(sprintf('%s object not found.', $class));
        }

        $request->attributes->set($name, $object);

        return true;
    }

    protected function getIdentifier(Request $request, $options, $name)
    {
        if (isset($options['id'])) {
            if (!is_array($options['id'])) {
                $name = $options['id'];
            } elseif (is_array($options['id'])) {
                $id = [];
                foreach ($options['id'] as $field) {
                    $id[$field] = $request->attributes->get($field);
                }

                return $id;
            }
        }

        if ($request->attributes->has($name)) {
            return $request->attributes->get($name);
        }

        if ($request->attributes->has('id') && !isset($options['id'])) {
            return $request->attributes->get('id');
        }

        return false;
    }

    public function supports(ParamConverter $configuration)
    {
        $options = $configuration->getOptions();
        /** @var ManagerInterface $manager */
        if (array_key_exists('manager', $options) && ($manager = $this->container->get($options['manager'])) instanceof ManagerInterface) {
            try {
                $manager->getRepository($configuration->getClass());

                return true;
            } catch (InvalidArgumentException $e) {
                return false;
            }
        }

        return false;
    }
}
