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

use FOS\RestBundle\View\View;


interface ViewHandlerInterface
{
    /**
     * @param RequestConfiguration $requestConfiguration
     * @param View $view
     *
     * @return mixed
     */
    public function handle(RequestConfiguration $requestConfiguration, View $view);
}
