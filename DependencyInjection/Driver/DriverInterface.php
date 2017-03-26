<?php

/*
 * This file is part of the Miky package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Miky\Bundle\ResourceBundle\DependencyInjection\Driver;

use Miky\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;


interface DriverInterface
{
    /**
     * @param ContainerBuilder $container
     * @param MetadataInterface $metadata
     */
    public function load(ContainerBuilder $container, MetadataInterface $metadata);

    /**
     * Returns unique name of the driver.
     *
     * @return string
     */
    public function getType();
}
