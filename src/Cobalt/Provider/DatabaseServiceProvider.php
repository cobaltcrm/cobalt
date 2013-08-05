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
use Joomla\Database\DatabaseDriver;

class DatabaseServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container->bind('db', function() {
                static $db;

                if (is_null($db)) {
                    $config = Container::get('config');

                    $options = array(
                        'driver' => $config->get('dbtype'),
                        'host' => $config->get('host'),
                        'user' => $config->get('user'),
                        'password' => $config->get('password'),
                        'database' => $config->get('db'),
                        'prefix' => $config->get('dbprefix')
                    );

                    $db = DatabaseDriver::getInstance($options);
                    $db->setDebug($config->get('debug', false));
                }

                return $db;
            });

        JFactory::$database = $container->resolve('db');
    }
}