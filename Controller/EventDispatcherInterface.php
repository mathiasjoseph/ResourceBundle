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

use Miky\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Miky\Component\Resource\Model\ResourceInterface;


interface EventDispatcherInterface
{
    /**
     * @param string $eventName
     * @param RequestConfiguration $requestConfiguration
     * @param ResourceInterface $resource
     *
     * @return ResourceControllerEvent
     */
    public function dispatch($eventName, RequestConfiguration $requestConfiguration, ResourceInterface $resource);

    /**
     * @param string $eventName
     * @param RequestConfiguration $requestConfiguration
     * @param ResourceInterface $resource
     *
     * @return ResourceControllerEvent
     */
    public function dispatchPreEvent($eventName, RequestConfiguration $requestConfiguration, ResourceInterface $resource);

    /**
     * @param string $eventName
     * @param RequestConfiguration $requestConfiguration
     * @param ResourceInterface $resource
     *
     * @return ResourceControllerEvent
     */
    public function dispatchPostEvent($eventName, RequestConfiguration $requestConfiguration, ResourceInterface $resource);
}
