<?php

/*
 * This file is part of the Miky package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Miky\Bundle\ResourceBundle\Form\Builder;

use Miky\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\Form\FormBuilderInterface;


interface DefaultFormBuilderInterface
{
    /**
     * @param MetadataInterface $metadata
     * @param FormBuilderInterface $formBuilder
     * @param array $options
     */
    public function build(MetadataInterface $metadata, FormBuilderInterface $formBuilder, array $options);
}
