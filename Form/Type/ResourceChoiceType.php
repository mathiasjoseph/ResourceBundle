<?php

/*
 * This file is part of the Miky package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Miky\Bundle\ResourceBundle\Form\Type;

use Miky\Bundle\ResourceBundle\MikyResourceBundle;
use Miky\Bundle\ResourceBundle\DependencyInjection\Driver\Exception\UnknownDriverException;
use Miky\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Aleksey Bannov <a.s.bannov@gmail.com>
 * @author Anna Walasek <anna.walasek@gmail.com>
 */
class ResourceChoiceType extends AbstractType
{
    /**
     * @var MetadataInterface
     */
    protected $metadata;

    /**
     * @param MetadataInterface $metadata
     */
    public function __construct(MetadataInterface $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'class' => null,
            ])
            ->setNormalizer('class', function () {
                return $this->metadata->getClass('model');
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->getFormTypeForDriver($this->metadata->getDriver());
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return sprintf('%s_%s_choice', $this->metadata->getApplicationName(), $this->metadata->getName());
    }

    /**
     * @param string $driver
     *
     * @return string
     *
     * @throws UnknownDriverException
     */
    protected function getFormTypeForDriver($driver)
    {
        switch ($driver) {
            case MikyResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM:
                return 'document';
            case MikyResourceBundle::DRIVER_DOCTRINE_ORM:
                return 'entity';
            case MikyResourceBundle::DRIVER_DOCTRINE_PHPCR_ODM:
                return 'phpcr_document';
        }

        throw new UnknownDriverException($driver);
    }
}
