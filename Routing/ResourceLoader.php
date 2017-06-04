<?php

/*
 * This file is part of the Miky package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Miky\Bundle\ResourceBundle\Routing;

use Miky\Component\Resource\Metadata\MetadataInterface;
use Miky\Component\Resource\Metadata\RegistryInterface;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Yaml\Yaml;


class ResourceLoader implements LoaderInterface
{
    /**
     * @var RegistryInterface
     */
    private $resourceRegistry;

    /**
     * @var RouteFactoryInterface
     */
    private $routeFactory;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param RegistryInterface $resourceRegistry
     * @param RouteFactoryInterface $routeFactory
     */
    public function __construct(RegistryInterface $resourceRegistry, RouteFactoryInterface $routeFactory, Container $container)
    {
        $this->resourceRegistry = $resourceRegistry;
        $this->routeFactory = $routeFactory;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        $processor = new Processor();
        $configurationDefinition = new Configuration();
        $configuration = Yaml::parse($resource);
        $configuration = $processor->processConfiguration($configurationDefinition, ['routing' => $configuration]);

        if (!empty($configuration['only']) && !empty($configuration['except'])) {
            throw new \InvalidArgumentException('You can configure only one of "except" & "only" options.');
        }

        $routesToGenerate = ['show', 'index', 'create', 'update', 'delete', 'batch'];

        if (!empty($configuration['only'])) {
            $routesToGenerate = $configuration['only'];
        }
        if (!empty($configuration['except'])) {
            $routesToGenerate = array_diff($routesToGenerate, $configuration['except']);
        }



        $isApi = $type === 'miky.resource_api';

        $metadata = $this->resourceRegistry->get($configuration['alias']);
        $routes = $this->routeFactory->createRouteCollection();

        $rootPath = sprintf('/%s/', isset($configuration['path']) ? $configuration['path'] : Urlizer::urlize($metadata->getPluralName()));

        if ($this->container->hasParameter("miky_admin.admin_path") && $type === 'miky.resource_admin'){
            $rootPath = $this->container->getParameter("miky_admin.admin_path") . $rootPath;
        }

        if (in_array('index', $routesToGenerate)) {
            $indexRoute = $this->createRoute($metadata, $configuration, $rootPath, 'index', ['GET'], $isApi);
            $routes->add($this->getRouteName($metadata, $configuration, 'index'), $indexRoute);
        }

        if (in_array('batch', $routesToGenerate)) {
            $batchRoute = $this->createRoute($metadata, $configuration, $isApi ? $rootPath : $rootPath . 'batch', 'batch', ['POST'], $isApi);
            $routes->add($this->getRouteName($metadata, $configuration, 'batch'), $batchRoute);
        }

        if (in_array('create', $routesToGenerate)) {
            $createRoute = $this->createRoute($metadata, $configuration, $isApi ? $rootPath : $rootPath . 'new', 'create', $isApi ? ['POST'] : ['GET', 'POST'], $isApi);
            $routes->add($this->getRouteName($metadata, $configuration, 'create'), $createRoute);
        }

        if (in_array('update', $routesToGenerate)) {
            $updateRoute = $this->createRoute($metadata, $configuration, $isApi ? $rootPath . '{id}' : $rootPath . '{id}/edit', 'update', $isApi ? ['PUT', 'PATCH', 'POST'] : ['GET', 'PUT', 'PATCH', 'POST'], $isApi);
            $routes->add($this->getRouteName($metadata, $configuration, 'update'), $updateRoute);
        }

        if (in_array('show', $routesToGenerate)) {
            $showRoute = $this->createRoute($metadata, $configuration, $rootPath . '{id}', 'show', ['GET'], $isApi);
            $routes->add($this->getRouteName($metadata, $configuration, 'show'), $showRoute);
        }

        if (in_array('delete', $routesToGenerate)) {
            $deleteRoute = $this->createRoute($metadata, $configuration, $rootPath . '{id}/remove', 'delete', ['GET', 'PUT', 'PATCH', 'POST', 'DELETE'], $isApi);
            $routes->add($this->getRouteName($metadata, $configuration, 'delete'), $deleteRoute);
        }

        return $routes;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return 'miky.resource' === $type || 'miky.resource_api' === $type || 'miky.resource_admin' === $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getResolver()
    {
        // Intentionally left blank.
    }

    /**
     * {@inheritdoc}
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
        // Intentionally left blank.
    }

    /**
     * @param MetadataInterface $metadata
     * @param array $configuration
     * @param string $path
     * @param string $actionName
     * @param array $methods
     *
     * @return Route
     */
    private function createRoute(MetadataInterface $metadata, array $configuration, $path, $actionName, array $methods, $isApi = false)
    {

        if (isset($configuration['context'])){
            if ($metadata->hasContexts() && $metadata->hasContext($configuration['context'])) {
                $defaults = [
                    '_controller' => $metadata->getServiceContextId('controller', $configuration['context']) . sprintf(':%sAction', $actionName),
                ];
            }
        }else{
            $defaults = [
                '_controller' => $metadata->getServiceId('controller').sprintf(':%sAction', $actionName),
            ];
        }

        if ($isApi && 'index' === $actionName) {
            $defaults['_miky']['serialization_groups'] = ['Default'];
        }
        if ($isApi && in_array($actionName, ['show', 'create', 'update'])) {
            $defaults['_miky']['serialization_groups'] = ['Default', 'Detailed'];
        }
        if (isset($configuration['grid']) && ('index' === $actionName || 'batch' === $actionName) ) {
            $defaults['_miky']['grid'] = $configuration['grid'];
        }
        if (isset($configuration['form']) && in_array($actionName, ['create', 'update'])) {
            $defaults['_miky']['form'] = $configuration['form'];
        }
        if (isset($configuration['section'])) {
            $defaults['_miky']['section'] = $configuration['section'];
        }
        if (isset($configuration['context'])) {
            $defaults['_miky']['context'] = $configuration['context'];
        }
        if (isset($configuration['templates']) && in_array($actionName, ['show', 'index', 'create', 'update'])) {
            $defaults['_miky']['template'] = sprintf('%s:%s.html.twig', $configuration['templates'], $actionName);
        }
        if (isset($configuration['redirect']) && in_array($actionName, ['create', 'update'])) {
            $defaults['_miky']['redirect'] = $this->getRouteName($metadata, $configuration, $configuration['redirect']);
        }
        if (isset($configuration['vars']['all'])) {
            $defaults['_miky']['vars'] = $configuration['vars']['all'];
        }
        if (isset($configuration['vars'][$actionName])) {
            $vars = isset($configuration['vars']['all']) ? $configuration['vars']['all'] : [];
            $defaults['_miky']['vars'] = array_merge($vars, $configuration['vars'][$actionName]);
        }

        return $this->routeFactory->createRoute($path, $defaults, [], [], '', [], $methods);
    }

    /**
     * @param MetadataInterface $metadata
     * @param array $configuration
     * @param string $actionName
     *
     * @return string
     */
    private function getRouteName(MetadataInterface $metadata, array $configuration, $actionName)
    {
        $sectionPrefix = isset($configuration['section']) ? $configuration['section'].'_' : '';

        return sprintf('%s_%s%s_%s', $metadata->getApplicationName(), $sectionPrefix, $metadata->getName(), $actionName);
    }
}
