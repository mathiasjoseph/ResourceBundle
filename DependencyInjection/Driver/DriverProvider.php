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

use Miky\Bundle\ResourceBundle\DependencyInjection\Driver\Doctrine\DoctrineORMDriver;
use Miky\Bundle\ResourceBundle\DependencyInjection\Driver\Exception\UnknownDriverException;
use Miky\Component\Resource\Metadata\MetadataInterface;

/**
 * @author Arnaud Langlade <aRn0D.dev@gmail.com>
 */
class DriverProvider
{
    /**
     * @param MetadataInterface $metadata
     *
     * @return DriverInterface
     *
     * @throws UnknownDriverException
     */
    public static function get(MetadataInterface $metadata)
    {
        return new DoctrineORMDriver();
    }
}
