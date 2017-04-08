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

use Miky\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Request configuration factory.
 *
 * @author Paweł Jędrzejewski <pawel@miky.org>
 */
interface RequestConfigurationFactoryInterface
{
    /**
     * @param MetadataInterface $metadata
     * @param Request $request
     *
     * @return RequestConfiguration
     *
     * @throws \InvalidArgumentException
     */
    public function create(MetadataInterface $metadata, Request $request);
}
