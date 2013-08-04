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
        $container->bind('db', function($c) {
                static $db;

                if (is_null($db)) {
                    /** @var Container $c */
                    $config = $c->resolve('config');

                    $options = array(
                        'driver' => $config->dbtype,
                        'host' => $config->host,
                        'user' => $config->user,
                        'password' => $config->password,
                        'database' => $config->db,
                        'prefix' => $config->dbprefix
                    );

                    $db = DatabaseDriver::getInstance($options);
                    $db->setDebug($config->debug);
                }

                return $db;
            });

        JFactory::$database = $container->resolve('db');
    }
}