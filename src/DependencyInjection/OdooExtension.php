<?php

namespace Refact\OdooBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class OdooExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('odoo.url', $config['url']);
        $container->setParameter('odoo.auth', [$config['database'], $config['admin_id'], $config['admin_pass']]);
        $container->setParameter('odoo.sso_secret', $config['sso_secret'] ?? null);
        $container->setParameter('odoo.role_mapping', $config['role_mapping']);
    }
}
