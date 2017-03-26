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
use Miky\Component\Grid\View\GridView;
use Miky\Component\Resource\Metadata\MetadataInterface;


class ResourceGridView extends GridView
{
    /**
     * @var MetadataInterface
     */
    private $metadata;

    /**
     * @var RequestConfiguration
     */
    private $requestConfiguration;

    /**
     * @param mixed $data
     * @param Grid $gridDefinition
     * @param Parameters $parameters
     * @param MetadataInterface $resourceMetadata
     * @param RequestConfiguration $requestConfiguration
     */
    public function __construct(
        $data,
        Grid $gridDefinition,
        Parameters $parameters,
        MetadataInterface $resourceMetadata,
        RequestConfiguration $requestConfiguration
    ) {
        parent::__construct($data, $gridDefinition, $parameters);

        $this->metadata = $resourceMetadata;
        $this->requestConfiguration = $requestConfiguration;
    }

    /**
     * @return MetadataInterface
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @return RequestConfiguration
     */
    public function getRequestConfiguration()
    {
        return $this->requestConfiguration;
    }
}
