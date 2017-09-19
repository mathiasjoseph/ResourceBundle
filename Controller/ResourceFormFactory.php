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

use Miky\Bundle\CoreBundle\Form\Factory\FormFactory;
use Miky\Component\Resource\Model\ResourceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactoryInterface;


class ResourceFormFactory implements ResourceFormFactoryInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory, ContainerInterface $container)
    {
        $this->formFactory = $formFactory;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function create(RequestConfiguration $requestConfiguration, ResourceInterface $resource)
    {
        $formType = $requestConfiguration->getFormType();
        $formOptions = $requestConfiguration->getFormOptions();

        if ($this->container->has($formType)){
            /** @var FormFactory $factory */
            $factory = $this->container->get($formType);
            if ($factory instanceof FormFactory){
                if ($requestConfiguration->isHtmlRequest()) {
                    return $factory->createForm($resource, $formOptions);
                }
                return $factory->createForm($resource, array_merge($formOptions, ['csrf_protection' => false]));

            }
        }


        if ($requestConfiguration->isHtmlRequest()) {
            return $this->formFactory->create($formType, $resource, $formOptions);
        }

        return $this->formFactory->create($formType, $resource, array_merge($formOptions, ['csrf_protection' => false]));
    }
}
