<?php

/*
 * This file is part of the Miky package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Miky\Bundle\ResourceBundle\DependencyInjection;

use Miky\Bundle\ResourceBundle\DependencyInjection\Driver\DriverProvider;
use Miky\Component\Resource\Metadata\Metadata;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @author Paweł Jędrzejewski <pawel@adevis.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class MikyResourceExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration($config, $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        
        $configFiles = [
            'services.xml',
            'controller.xml',
            'storage.xml',
            'routing.xml',
            'twig.xml',
        ];

        foreach ($configFiles as $configFile) {
            $loader->load($configFile);
        }

        $bundles = $container->getParameter('kernel.bundles');
        if (array_key_exists('MikyGridBundle', $bundles)) {
            $loader->load('grid.xml');
        }

        if ($config['translation']['enabled']) {
            $loader->load('translation.xml');

            $container->setParameter('adevis.translation.default_locale', $config['translation']['default_locale']);
            $container->setAlias('adevis.translation.locale_provider', $config['translation']['locale_provider']);
            $container->setAlias('adevis.translation.available_locales_provider', $config['translation']['available_locales_provider']);
            $container->setParameter('adevis.translation.available_locales', $config['translation']['available_locales']);
        }

        $container->setParameter('adevis.resource.settings', $config['settings']);
        $container->setAlias('adevis.resource_controller.authorization_checker', $config['authorization_checker']);

        $this->loadPersistence($config['drivers'], $config['resources'], $loader);
        $this->loadResources($config['resources'], $container);
    }

    private function loadPersistence(array $enabledDrivers, array $resources, LoaderInterface $loader)
    {
        foreach ($resources as $alias => $resource) {
            if (!in_array($resource['driver'], $enabledDrivers)) {
                throw new InvalidArgumentException(sprintf(
                    'Resource "%s" uses driver "%s", but this driver has not been enabled.',
                    $alias, $resource['driver']
                ));
            }
        }

        foreach ($enabledDrivers as $enabledDriver) {
            $loader->load(sprintf('driver/%s.xml', $enabledDriver));
        }
    }

    private function loadResources(array $resources, ContainerBuilder $container)
    {
        foreach ($resources as $alias => $resourceConfig) {
            $metadata = Metadata::fromAliasAndConfiguration($alias, $resourceConfig);

            $resources = $container->hasParameter('adevis.resources') ? $container->getParameter('adevis.resources') : [];
            $resources = array_merge($resources, [$alias => $resourceConfig]);
            $container->setParameter('adevis.resources', $resources);

            DriverProvider::get($metadata)->load($container, $metadata);

            if ($metadata->hasParameter('translation')) {
                $alias = $alias.'_translation';
                $resourceConfig = array_merge(['driver' => $resourceConfig['driver']], $resourceConfig['translation']);

                $resources = $container->hasParameter('adevis.resources') ? $container->getParameter('adevis.resources') : [];
                $resources = array_merge($resources, [$alias => $resourceConfig]);
                $container->setParameter('adevis.resources', $resources);

                $metadata = Metadata::fromAliasAndConfiguration($alias, $resourceConfig);

                DriverProvider::get($metadata)->load($container, $metadata);
            }
        }
    }
}
