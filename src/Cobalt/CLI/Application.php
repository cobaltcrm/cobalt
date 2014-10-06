<?php
/**
 * @package    Cobalt.CRM
 *
 * @copyright  Copyright (C) 2012 Cobalt. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Cobalt\CLI;

defined('_CEXEC') or die;

use Cobalt\Container;
use Cobalt\Provider\ApplicationServiceProvider;
use Cobalt\Provider\ConfigServiceProvider;
use Cobalt\Provider\DatabaseServiceProvider;

use Joomla\Application\AbstractCliApplication;

/**
 * CLI application supporting the base application
 *
 * @since  1.0
 */
class Application extends AbstractCliApplication
{
	/**
	 * DI Container
	 *
	 * @var    Container
	 * @since  1.0
	 */
	private $container;

	/**
	 * Constructor
	 *
	 * @since   1.0
	 */
	public function __construct()
	{
		$container = Container::getInstance();

		$container->registerServiceProvider(new ApplicationServiceProvider($this))
			->registerServiceProvider(new ConfigServiceProvider)
			->registerServiceProvider(new DatabaseServiceProvider);

		$this->setContainer($container);

		// Set error reporting based on config
		$errorReporting = (int) $container->get('config')->get('errorReporting', 0);
		error_reporting($errorReporting);

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
		$this->out('Finished!');
	}

	/**
	 * Get the DI container.
	 *
	 * @return  Container
	 *
	 * @since   1.0
	 * @throws  \UnexpectedValueException May be thrown if the container has not been set.
	 */
	public function getContainer()
	{
		if ($this->container)
		{
			return $this->container;
		}

		throw new \UnexpectedValueException('Container not set in ' . __CLASS__);
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
		$this->container = $container;

		return $this;
	}
}
