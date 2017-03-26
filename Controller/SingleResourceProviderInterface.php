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
use Miky\Component\Resource\Repository\RepositoryInterface;


interface SingleResourceProviderInterface
{
    /**
     * @param RequestConfiguration $requestConfiguration
     * @param RepositoryInterface $repository
     *
     * @return ResourceInterface|null
     */
    public function get(RequestConfiguration $requestConfiguration, RepositoryInterface $repository);
}
