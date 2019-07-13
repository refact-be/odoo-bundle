<?php

namespace Refact\OdooBundle;

use Refact\Odoo\Odoo;
use Refact\OdooBundle\Command\GetRolesCommand;
use Refact\OdooBundle\DependencyInjection\Security\Factory\FormLoginOdooFactory;
use Refact\OdooBundle\DependencyInjection\Security\Factory\SsoOdooFactory;
use Refact\OdooBundle\Security\Http\Firewall\SsoAuthenticationListener;
use Refact\OdooBundle\Security\OdooAuthenticationProvider;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class OdooBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        /** @var SecurityExtension $extension */
        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new FormLoginOdooFactory());
        $extension->addSecurityListenerFactory(new SsoOdooFactory());

        $container->register(Odoo::class)
            ->setArguments(['%odoo.url%', '%odoo.auth%']);

        $container->autowire(TokenFactory::class)
            ->setArgument('$roleMapping', '%odoo.role_mapping%');

        $container->autowire(SsoHelper::class)
            ->setArguments([['url' => '%odoo.url%', 'sso_secret' => '%odoo.sso_secret%']]);

        $container->autowire(OdooAuthenticationProvider::class);

        $container->setDefinition('security.authentication.listener.odoo', (new ChildDefinition('security.authentication.listener.abstract'))
            ->setClass(SsoAuthenticationListener::class)
            ->addMethodCall('setSsoHelper', [new Reference(SsoHelper::class)])
            ->setAbstract(true));

        $container->autowire(GetRolesCommand::class)
            ->setAutoconfigured(true);
    }
}
