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

use Miky\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Miky\Component\Grid\Definition\Grid;
use Miky\Component\Grid\Parameters;
use Miky\Component\Resource\Metadata\MetadataInterface;


interface ResourceGridViewFactoryInterface
{
    /**
     * @param Grid $grid
     * @param Parameters $parameters
     * @param MetadataInterface $metadata
     * @param RequestConfiguration $requestConfiguration
     *
     * @return ResourceGridView
     */
    public function create(Grid $grid, Parameters $parameters, MetadataInterface $metadata, RequestConfiguration $requestConfiguration);
}
