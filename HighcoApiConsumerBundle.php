<?php

namespace Highco\ApiConsumerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Highco\ApiConsumerBundle\DependencyInjection\ApiConsumerBundleExtension;
use Highco\ApiConsumerBundle\DependencyInjection\Compiler\EventHandlerCompilerPass;

class HighcoApiConsumerBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new EventHandlerCompilerPass());
    }

    /**
     * @return ApiConsumerBundleExtension|ExtensionInterface
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {

            $extension = new ApiConsumerBundleExtension();

            if (!$extension instanceof ExtensionInterface) {

                $message = sprintf('%s is not a instance of ExtensionInterface', ApiConsumerBundleExtension::class);

                throw new \LogicException($message);
            }

            $this->extension = $extension;
        }

        return $this->extension;
    }
}
