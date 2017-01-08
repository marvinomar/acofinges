<?php
// src/Application/CoreBundle/DependencyInjection/Security/Factory/CoreSecurityFactoryInterface.php
namespace Application\CoreBundle\DependencyInjection\Security\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface as SymfonySecFacInt;

class CoreSecurityFactoryInterface implements SymfonySecFacInt {

    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint) {
        $providerId = 'security.authentication.provider.core.'.$id;
        $container
            ->setDefinition($providerId, new DefinitionDecorator('core.security.authentication.provider'))
            ->replaceArgument(0, new Reference($userProvider))
        ;

        $listenerId = 'security.authentication.listener.core.'.$id;
        $listener = $container->setDefinition($listenerId, new DefinitionDecorator('core.security.authentication.listener'));

        return array($providerId, $listenerId, $defaultEntryPoint);
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'rsa';
    }

    public function addConfiguration(NodeDefinition $node)
    {
    }
}