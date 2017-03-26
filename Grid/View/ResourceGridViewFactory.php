<?php

/*
 * This file is part of the Miky package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Miky\Bundle\ResourceBundle\Grid\View;

use Miky\Bundle\ResourceBundle\Controller\ParametersParserInterface;
use Miky\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Miky\Component\Grid\Data\DataProviderInterface;
use Miky\Component\Grid\Definition\Grid;
use Miky\Component\Grid\Parameters;
use Miky\Component\Resource\Metadata\MetadataInterface;


class ResourceGridViewFactory implements ResourceGridViewFactoryInterface
{
    /**
     * @var DataProviderInterface
     */
    private $dataProvider;

    /**
     * @var ParametersParserInterface
     */
    private $parametersParser;

    /**
     * @param DataProviderInterface $dataProvider
     * @param ParametersParserInterface $parametersParser
     */
    public function __construct(DataProviderInterface $dataProvider, ParametersParserInterface $parametersParser)
    {
        $this->dataProvider = $dataProvider;
        $this->parametersParser = $parametersParser;
    }

    /**
     * {@inheritdoc}
     */
    public function create(Grid $grid, Parameters $parameters, MetadataInterface $metadata, RequestConfiguration $requestConfiguration)
    {
        $driverConfiguration = $grid->getDriverConfiguration();
        $request = $requestConfiguration->getRequest();

        $grid->setDriverConfiguration($this->parametersParser->parseRequestValues($driverConfiguration, $request));

        return new ResourceGridView($this->dataProvider->getData($grid, $parameters), $grid, $parameters, $metadata, $requestConfiguration);
    }
}
