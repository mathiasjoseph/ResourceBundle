<?php

/*
 * This file is part of the Miky package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Miky\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\Form\Builder;

use Miky\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\Form\Subscriber\DefaultPathSubscriber;
use Miky\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\Form\Subscriber\NameResolverSubscriber;
use Miky\Bundle\ResourceBundle\Form\Builder\DefaultFormBuilderInterface;
use Miky\Component\Resource\Metadata\MetadataInterface;
use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use Doctrine\ODM\PHPCR\Mapping\ClassMetadata;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Daniel Leech <daniel@dantleech.com>
 */
class DefaultFormBuilder implements DefaultFormBuilderInterface
{
    /**
     * @var DocumentManagerInterface
     */
    private $documentManager;

    /**
     * @param DocumentManagerInterface $documentManager
     */
    public function __construct(
        DocumentManagerInterface $documentManager
    )
    {
        $this->documentManager = $documentManager;
    }

    /**
     * {@inheritdoc}
     */
    public function build(MetadataInterface $metadata, FormBuilderInterface $formBuilder, array $options)
    {
        $classMetadata = $this->documentManager->getClassMetadata($metadata->getClass('model'));

        // the field mappings should only contain standard value mappings
        foreach ($classMetadata->fieldMappings as $fieldName) {
            if ($fieldName === $classMetadata->uuidFieldName) {
                continue;
            }
            if ($fieldName === $classMetadata->nodename) {
                continue;
            }

            $options = [];

            $mapping = $classMetadata->mappings[$fieldName];

            if ($mapping['nullable'] === false) {
                $options['required'] = true;
            }

            $formBuilder->add($fieldName, null, $options);
        }
    }
}
