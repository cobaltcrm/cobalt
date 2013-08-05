<?php
/**
 * @package    Cobalt.CRM
 *
 * @copyright  Copyright (C) 2013 Webspark, LLC. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Cobalt\Provider;

use JFactory;
use Cobalt\Container;
use Symfony\Component\HttpFoundation\Session\Session;

class SessionServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $config = $container->resolve('config');

        if ($config->get('session', true) !== false) {

            $session = new Session;

            $session->start();

            // @TODO Remove JFactory
            JFactory::$session = $session;

            $container->bind('session', function () use ($session) {
                    return $session;
                });
        }
    }
}