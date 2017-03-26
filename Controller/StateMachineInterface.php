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


interface StateMachineInterface
{
    /**
     * @param RequestConfiguration $configuration
     * @param ResourceInterface $resource
     *
     * @return bool
     */
    public function can(RequestConfiguration $configuration, ResourceInterface $resource);

    /**
     * @param RequestConfiguration $configuration
     * @param ResourceInterface $resource
     */
    public function apply(RequestConfiguration $configuration, ResourceInterface $resource);
}
