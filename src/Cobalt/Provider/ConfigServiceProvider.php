<?php
/**
 * @package    Cobalt.CRM
 *
 * @copyright  Copyright (C) 2013 Webspark, LLC. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Cobalt\Provider;

use JConfig;
use JFactory;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Registry\Registry;

/**
 * Configuration service provider
 *
 * @since  1.0
 */
class ConfigServiceProvider implements ServiceProviderInterface
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
		require_once JPATH_CONFIGURATION . '/configuration.php';

		$config = new Registry(new JConfig);

		// Set the error_reporting
		switch ($config->get('error_reporting'))
		{
			case 'default':
			case '-1':
				break;

			case 'none':
			case '0':
				error_reporting(0);
				break;

			case 'simple':
				error_reporting(E_ERROR | E_WARNING | E_PARSE);
				ini_set('display_errors', 1);
				break;

			case 'maximum':
				error_reporting(E_ALL);
				ini_set('display_errors', 1);
				break;

			case 'development':
				error_reporting(-1);
				ini_set('display_errors', 1);
				break;

			default:
				error_reporting($config->get('error_reporting'));
				ini_set('display_errors', 1);
				break;
		}

		JFactory::$config = $config;

		define('JDEBUG', $config->get('debug', false));

		$container->protect('config', function () use ($config) {
			return $config;
		}, true);
	}
}
