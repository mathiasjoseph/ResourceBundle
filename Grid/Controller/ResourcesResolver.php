<?php

/*
 * This file is part of the Miky package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Miky\Bundle\ResourceBundle\Grid\Controller;

use Miky\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Miky\Bundle\ResourceBundle\Controller\ResourcesResolverInterface;
use Miky\Bundle\ResourceBundle\Grid\View\ResourceGridViewFactoryInterface;
use Miky\Component\Grid\Parameters;
use Miky\Component\Grid\Provider\GridProviderInterface;
use Miky\Component\Resource\Repository\RepositoryInterface;


class ResourcesResolver implements ResourcesResolverInterface
{
    /**
     * @var ResourcesResolverInterface
     */
    private $decoratedResolver;

    /**
     * @var GridProviderInterface
     */
    private $gridProvider;

    /**
     * @var ResourceGridViewFactoryInterface
     */
    private $gridViewFactory;

    /**
     * @param ResourcesResolverInterface $decoratedResolver
     * @param GridProviderInterface $gridProvider
     * @param ResourceGridViewFactoryInterface $gridViewFactory
     */
    public function __construct(
        ResourcesResolverInterface $decoratedResolver,
        GridProviderInterface $gridProvider,
        ResourceGridViewFactoryInterface $gridViewFactory
    ) {
        $this->decoratedResolver = $decoratedResolver;
        $this->gridProvider = $gridProvider;
        $this->gridViewFactory = $gridViewFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getResources(RequestConfiguration $requestConfiguration, RepositoryInterface $repository)
    {
        if (!$requestConfiguration->hasGrid()) {
            return $this->decoratedResolver->getResources($requestConfiguration, $repository);
        }

        $gridDefinition = $this->gridProvider->get($requestConfiguration->getGrid());

        $request = $requestConfiguration->getRequest();
        $parameters = new Parameters($request->query->all());

        $gridView = $this->gridViewFactory->create($gridDefinition, $parameters, $requestConfiguration->getMetadata(), $requestConfiguration);

        if ($requestConfiguration->isHtmlRequest()) {
            return $gridView;
        }

        return $gridView->getData();
    }


}
