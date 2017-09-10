<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hgabka\KunstmaanFrontendUserBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @see http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class HgabkaKunstmaanFrontendUserExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('hgabka_kunstmaan_frontend_user_class', $config['user_class']);
        $container->setParameter('hgabka_kunstmaan_frontend_firewall_name', $config['firewall_name']);
        $container->setParameter('hgabka_kunstmaan_frontend_resetting_form_type', $config['resetting']['form_type']);
        $container->setParameter('hgabka_kunstmaan_frontend_resetting_email_template', $config['resetting']['email_template']);
        $container->setParameter('hgabka_kunstmaan_frontend_registration_form_type', $config['registration']['form_type']);
        $container->setParameter('hgabka_kunstmaan_frontend_registration_confirmation_email_template', $config['registration']['confirmation']['email_template']);
        $container->setParameter('hgabka_kunstmaan_frontend_registration_confirmation_enabled', $config['registration']['confirmation']['enabled']);
        $container->setParameter('hgabka_kunstmaan_frontend_profile_form_type', $config['profile']['form_type']);
        $container->setParameter('hgabka_kunstmaan_frontend_email', [$config['email']['address'] => $config['email']['sender_name']]);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
