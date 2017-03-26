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

use Doctrine\ODM\MongoDB\Event\LoadClassMetadataEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadataInfo;

/**
 * Doctrine listener used to manipulate mappings.
 *
 * @author Ivannis Suárez Jérez <ivannis.suarez@gmail.com>
 */
class ODMMappedSuperClassSubscriber extends AbstractDoctrineSubscriber
{
    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata,
        ];
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $metadata = $eventArgs->getClassMetadata();

        $this->convertToDocumentIfNeeded($metadata);

        if (!$metadata->isMappedSuperclass) {
            $this->setAssociationMappings($metadata, $eventArgs->getDocumentManager()->getConfiguration());
        } else {
            $this->unsetAssociationMappings($metadata);
        }
    }

    /**
     * @param ClassMetadataInfo $metadata
     */
    private function convertToDocumentIfNeeded(ClassMetadataInfo $metadata)
    {
        if (false === $metadata->isMappedSuperclass) {
            return;
        }

        try {
            $resourceMetadata = $this->resourceRegistry->getByClass($metadata->getName());
        } catch (\InvalidArgumentException $exception) {
            return;
        }

        if ($metadata->getName() === $resourceMetadata->getClass('model')) {
            $metadata->isMappedSuperclass = false;
        }
    }

    /**
     * @param ClassMetadataInfo $metadata
     * @param $configuration
     */
    private function setAssociationMappings(ClassMetadataInfo $metadata, $configuration)
    {
        foreach (class_parents($metadata->getName()) as $parent) {
            $parentMetadata = new ClassMetadata($parent);

            if (false === $this->isMikyClass($parentMetadata)) {
                continue;
            }

            if (false === in_array($parent, $configuration->getMetadataDriverImpl()->getAllClassNames())) {
                continue;
            }

            $configuration->getMetadataDriverImpl()->loadMetadataForClass($parent, $parentMetadata);
            if ($parentMetadata->isMappedSuperclass) {
                foreach ($parentMetadata->associationMappings as $key => $value) {
                    if ($this->hasRelation($value['association'])) {
                        $metadata->associationMappings[$key] = $value;
                    }
                }
            }

        }
    }

    /**
     * @param ClassMetadataInfo $metadata
     */
    private function unsetAssociationMappings(ClassMetadataInfo $metadata)
    {
        if (false === $this->isMikyClass($metadata)) {
            return;
        }

        foreach ($metadata->associationMappings as $key => $value) {
            if ($this->hasRelation($value['association'])) {
                unset($metadata->associationMappings[$key]);
            }
        }
    }

    /**
     * @param $type
     *
     * @return bool
     */
    private function hasRelation($type)
    {
        return in_array(
            $type,
            [
                ClassMetadataInfo::REFERENCE_ONE,
                ClassMetadataInfo::REFERENCE_MANY,
                ClassMetadataInfo::EMBED_ONE,
                ClassMetadataInfo::EMBED_MANY,
            ],
            true
        );
    }
}
