<?php

namespace Refact\OdooBundle\DependencyInjection\Security\Factory;

use Refact\OdooBundle\Security\OdooAuthenticationProvider;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SsoOdooFactory extends AbstractFactory
{
    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        return OdooAuthenticationProvider::class;
    }

    protected function getListenerId()
    {
        return 'security.authentication.listener.odoo';
    }

    public function getPosition()
    {
        return 'remember_me';
    }

    public function getKey()
    {
        return 'sso-odoo';
    }

    protected function createEntryPoint($container, $id, $config, $defaultEntryPoint)
    {
        $entryPointId = 'security.authentication.form_entry_point.'.$id;
        $container
            ->setDefinition($entryPointId, new ChildDefinition('security.authentication.form_entry_point'))
            ->addArgument(new Reference('security.http_utils'))
            ->addArgument($config['login_path'])
            ->addArgument($config['use_forward'])
        ;

        return $entryPointId;
    }
}
