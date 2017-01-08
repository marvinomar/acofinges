<?php

namespace Application\CoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Application\CoreBundle\DependencyInjection\Security\Factory\CoreSecurityFactoryInterface;

class ApplicationCoreBundle extends Bundle {

    public function build(ContainerBuilder $container) {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new CoreSecurityFactoryInterface());
    }
}
