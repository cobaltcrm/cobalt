<?php
/**
 * @package    Cobalt.CRM
 *
 * @copyright  Copyright (C) 2013 Webspark, LLC. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Cobalt\Provider;

use JFactory;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Registry\Registry;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Configuration service provider
 *
 * @since  1.0
 */
class SessionServiceProvider implements ServiceProviderInterface
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
		$config = $container->get('config');

		if ($config->get('session', true) !== false)
		{
			$session = new Session;

			$session->start();

			$registry = $session->get('registry');

            if (is_null($registry))
            {
                $session->set('registry', new Registry('session'));
            }

			// @TODO Remove JFactory
			JFactory::$session = $session;

			$container->set('session', function () use ($session) {
				return $session;
			}, true, true);
		}
	}
}
