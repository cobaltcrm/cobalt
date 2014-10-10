<?php
/**
 * Cobalt CRM
 *
 * @copyright  Copyright (C) 2012 - 2014 cobaltcrm.org All Rights Reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Cobalt;

/**
 * Cobalt Factory
 *
 * @since  1.0
 */
abstract class Factory
{
	/**
	 * Fetch the Application object
	 *
	 * @return  Application
	 *
	 * @since   1.0
	 */
	public static function getApplication()
	{
		return self::getContainer()->get('app');
	}

	/**
	 * Fetch the DI container
	 *
	 * @return  \Joomla\DI\Container
	 *
	 * @since   1.0
	 */
	private static function getContainer()
	{
		return Application::getDIContainer();
	}

	/**
	 * Fetch the DatabaseDriver object
	 *
	 * @return  \Joomla\Database\DatabaseDriver
	 *
	 * @since   1.0
	 */
	public static function getDb()
	{
		return self::getContainer()->get('db');
	}

	/**
	 * Fetch the Session object
	 *
	 * @return  \Symfony\Component\HttpFoundation\Session\Session
	 *
	 * @since   1.0
	 */
	public static function getSession()
	{
		return self::getApplication()->getSession();
	}
}
