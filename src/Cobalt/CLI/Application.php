<?php
/**
 * @package    Cobalt.CRM
 *
 * @copyright  Copyright (C) 2012 Cobalt. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Cobalt\CLI;

defined('_CEXEC') or die;

use Cobalt\CLI\Command\Install;
use Cobalt\Provider\ApplicationServiceProvider;
use Cobalt\Provider\ConfigServiceProvider;
use Cobalt\Provider\DatabaseServiceProvider;

use Joomla\Application\AbstractCliApplication;
use Joomla\DI\Container;
use Joomla\DI\ContainerAwareInterface;
use Joomla\Language\Language;
use Joomla\Language\Text;

/**
 * CLI application supporting the base application
 *
 * @since  1.0
 */
class Application extends AbstractCliApplication implements ContainerAwareInterface
{
	/**
	 * DI Container
	 *
	 * @var    Container
	 * @since  1.0
	 */
	private static $container;

	/**
	 * The Language object
	 *
	 * @var    Language
	 * @since  1.0
	 */
	private $language;

	/**
	 * Constructor
	 *
	 * @since   1.0
	 */
	public function __construct()
	{
		$container = new Container;

		$container->registerServiceProvider(new ApplicationServiceProvider($this));

		$this->setContainer($container);

		parent::__construct();
	}

	/**
	 * Method to run the application routines.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	protected function doExecute()
	{
		// If --install option provided, run the install routine to set up the database
		if ($this->input->getBool('install', false))
		{
			$command = new Install($this);
			$command->execute();
		}

		// If a non-install CLI script is added, un-comment these lines or otherwise integrate them into the routines
		/* $container->registerServiceProvider(new ConfigServiceProvider)
			->registerServiceProvider(new DatabaseServiceProvider);

        // Set error reporting based on config
		$errorReporting = (int) $container->get('config')->get('errorReporting', 0);
		error_reporting($errorReporting); */
	}

	/**
	 * Get the DI container
	 *
	 * @return  Container
	 *
	 * @since   1.0
	 * @throws  \UnexpectedValueException May be thrown if the container has not been set.
	 */
	public function getContainer()
	{
		return static::getDIContainer();
	}

	/**
	 * Get the DI container
	 *
	 * @return  Container
	 *
	 * @since   1.0
	 * @throws  \UnexpectedValueException May be thrown if the container has not been set.
	 */
	public static function getDIContainer()
	{
		if (static::$container)
		{
			return static::$container;
		}

		throw new \UnexpectedValueException('Container not set in ' . __CLASS__);
	}

	/**
	 * Get a language object.
	 *
	 * @return Language
	 *
	 * @since   1.0
	 */
	public function getLanguage()
	{
		if (is_null($this->language)) {
			$this->language = Language::getInstance('en-GB');

			// Configure Text to use language instance
			Text::setLanguage($this->language);
		}

		return $this->language;
	}

	/**
	 * Execute a command on the server.
	 *
	 * @param   string  $command  The command to execute.
	 *
	 * @return  string  Return data from the command
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function runCommand($command)
	{
		$lastLine = system($command, $status);

		if ($status)
		{
			// Command exited with a status != 0
			if ($lastLine)
			{
				$this->out($lastLine);

				throw new \RuntimeException($lastLine);
			}

			$this->out('An unknown error occurred');

			throw new \RuntimeException('An unknown error occurred');
		}

		return $lastLine;
	}

	/**
	 * Set the DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  $this  Method allows chaining
	 *
	 * @since   1.0
	 */
	public function setContainer(Container $container)
	{
		static::$container = $container;

		return $this;
	}
}
