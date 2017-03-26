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

use Miky\Component\Resource\Metadata\RegistryInterface;
use Miky\Component\Resource\Provider\LocaleProviderInterface;


abstract class AbstractTranslatableListener
{
    /**
     * @var RegistryInterface
     */
    protected $registry;

    /**
     * @var LocaleProviderInterface
     */
    protected $localeProvider;

    /**
     * @param RegistryInterface $registry
     * @param LocaleProviderInterface $localeProvider
     */
    public function __construct(RegistryInterface $registry, LocaleProviderInterface $localeProvider)
    {
        $this->registry = $registry;
        $this->localeProvider = $localeProvider;
    }
}
