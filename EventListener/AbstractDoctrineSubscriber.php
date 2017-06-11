<?php

/*
 * This file is part of the Miky package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Miky\Bundle\ResourceBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Miky\Component\Resource\Metadata\RegistryInterface;

/**
 * @author Ben Davies <ben.davies@gmail.com>
 */
abstract class AbstractDoctrineSubscriber implements EventSubscriber
{
    /**
     * @var RegistryInterface
     */
    protected $resourceRegistry;

    /**
     * @param RegistryInterface $resourceRegistry
     */
    public function __construct(RegistryInterface $resourceRegistry)
    {
        $this->resourceRegistry = $resourceRegistry;
    }

    /**
     * @param ClassMetadata $metadata
     *
     * @return bool
     */
    protected function isMikyClass(ClassMetadata $metadata)
    {
        return 0 === strpos($metadata->getName(), 'Miky\\');
    }
}
