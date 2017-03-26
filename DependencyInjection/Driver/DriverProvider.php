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

use Miky\Bundle\ResourceBundle\MikyResourceBundle;
use Miky\Bundle\ResourceBundle\DependencyInjection\Driver\Doctrine\DoctrineODMDriver;
use Miky\Bundle\ResourceBundle\DependencyInjection\Driver\Doctrine\DoctrineORMDriver;
use Miky\Bundle\ResourceBundle\DependencyInjection\Driver\Doctrine\DoctrinePHPCRDriver;
use Miky\Bundle\ResourceBundle\DependencyInjection\Driver\Exception\UnknownDriverException;
use Miky\Component\Resource\Metadata\MetadataInterface;

/**
 * @author Arnaud Langlade <aRn0D.dev@gmail.com>
 */
class DriverProvider
{
    /**
     * @var DriverInterface[]
     */
    private static $drivers = [];

    /**
     * @param MetadataInterface $metadata
     *
     * @return DriverInterface
     *
     * @throws UnknownDriverException
     */
    public static function get(MetadataInterface $metadata)
    {
        $type = $metadata->getDriver();

        if (isset(self::$drivers[$type])) {
            return self::$drivers[$type];
        }

        switch ($type) {
            case MikyResourceBundle::DRIVER_DOCTRINE_ORM:
                return self::$drivers[$type] = new DoctrineORMDriver();
            case MikyResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM:
                return self::$drivers[$type] = new DoctrineODMDriver();
            case MikyResourceBundle::DRIVER_DOCTRINE_PHPCR_ODM:
                return self::$drivers[$type] = new DoctrinePHPCRDriver();
        }

        throw new UnknownDriverException($type);
    }
}
