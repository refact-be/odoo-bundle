<?php

namespace Refact\OdooBundle\DependencyInjection\Security\Factory;

use Refact\OdooBundle\Security\OdooAuthenticationProvider;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\FormLoginFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FormLoginOdooFactory extends FormLoginFactory
{
    public function getKey()
    {
        return 'form-login-odoo';
    }

    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        return OdooAuthenticationProvider::class;
    }
}
