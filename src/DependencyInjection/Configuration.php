<?php

namespace Hgabka\KunstmaanFrontendUserBundle\DependencyInjection;

use Hgabka\KunstmaanFrontendUserBundle\Form\ProfileFormType;
use Hgabka\KunstmaanFrontendUserBundle\Form\RegistrationFormType;
use Hgabka\KunstmaanFrontendUserBundle\Form\ResettingFormType;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('hgabka_kunstmaan_frontend_user');

        $rootNode
            ->children()
                ->scalarNode('user_class')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('firewall_name')->isRequired()->cannotBeEmpty()->end()
                ->arrayNode('email')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('address')->isRequired()->cannotBeEmpty()->defaultValue('info@example.com')->end()
                        ->scalarNode('sender_name')->isRequired()->cannotBeEmpty()->defaultValue('Acme Company')->end()
                    ->end()
                ->end()
                ->arrayNode('registration')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->arrayNode('confirmation')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultFalse()->end()
                                ->scalarNode('email_template')->defaultValue('HgabkaKunstmaanFrontendUserBundle:Registration:frontend_email.txt.twig')->end()
                            ->end()
                        ->end()
                        ->scalarNode('form_type')->defaultValue(RegistrationFormType::class)->end()
                    ->end()
                ->end()
                ->arrayNode('resetting')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('email_template')->defaultValue('HgabkaKunstmaanFrontendUserBundle:Resetting:frontend_email.txt.twig')->end()
                        ->scalarNode('form_type')->defaultValue(ResettingFormType::class)->end()
                    ->end()
                ->end()
                ->arrayNode('profile')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('form_type')->defaultValue(ProfileFormType::class)->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
