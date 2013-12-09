<?php
/**
* @package    Cobalt.CRM
*
* @copyright  Copyright (C) 2013 Webspark, LLC. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

namespace Cobalt\Provider;

use Cobalt\Container;

interface ServiceProviderInterface
{
    public function register(Container $container);
}
