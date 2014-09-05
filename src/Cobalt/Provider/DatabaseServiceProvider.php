<?php
/**
 * @package    Cobalt.CRM
 *
 * @copyright  Copyright (C) 2013 Webspark, LLC. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Cobalt\Provider;

use JFactory;
use Joomla\Database\DatabaseDriver;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

/**
 * Database service provider
 *
 * @since  1.0
 */
class DatabaseServiceProvider implements ServiceProviderInterface
{
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
    public function register(Container $container)
    {
	    $container->set('Joomla\\Database\\DatabaseDriver',
            function () use ($container)
            {
                $config = $container->get('config');

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

                return $db;
            }, true, true
        );

        // Alias the database
        $container->alias('db', 'Joomla\\Database\\DatabaseDriver');

        JFactory::$database = $container->get('db');
    }
}
