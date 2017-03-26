<?php

/*
 * This file is part of the Miky package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Miky\Bundle\ResourceBundle\Controller;

use Miky\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Form\FormFactoryInterface;


class ResourceFormFactory implements ResourceFormFactoryInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create(RequestConfiguration $requestConfiguration, ResourceInterface $resource)
    {
        $formType = $requestConfiguration->getFormType();

        if (false !== strpos($formType, '\\')) {
            $formType = new $formType();
        }

        $formOptions = $requestConfiguration->getFormOptions();

        if ($requestConfiguration->isHtmlRequest()) {
            return $this->formFactory->create($formType, $resource, $formOptions);
        }

        return $this->formFactory->createNamed('', $formType, $resource, array_merge($formOptions, ['csrf_protection' => false]));
    }
}
