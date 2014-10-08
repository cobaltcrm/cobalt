<?php
/**
 * @package    Cobalt.CRM
 *
 * @copyright  Copyright (C) 2012 Cobalt. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Cobalt\CLI\Command;

defined('_CEXEC') or die;

use Cobalt\CLI\Application;

use Joomla\Filesystem\File;

/**
 * Class to install the application.
 *
 * @since  1.0
 */
class Install
{
	/**
	 * Application object
	 *
	 * @var    Application
	 * @since  1.0
	 */
	private $app;

	/**
	 * Class constructor
	 *
	 * @param   Application  $app  Application object
	 *
	 * @since   1.0
	 */
	public function __construct(Application $app)
	{
		$this->app = $app;
	}

	/**
	 * Execute the command.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  AbortException
	 * @throws  \RuntimeException
	 * @throws  \UnexpectedValueException
	 */
	public function execute()
	{
		$model = new \Cobalt\Model\Install;

		// Figure out if we have pre-loaded install data or if we'll prompt the user for data
		if ($this->app->input->getBool('datafile') && file_exists(JPATH_CONFIGURATION . '/install_data.txt'))
		{
			// Load up the pre-loaded data
			$data = unserialize(file_get_contents(JPATH_CONFIGURATION . '/install_data.txt'));
		}
		else
		{
			// Need to prompt user for data
			$data = array();

			$drivers = $model->dboDrivers();

			$this->app->out('Please select your database driver. Valid options are: ' . implode(', ', $drivers));
			$data['db_drive'] = trim($this->app->in());

			if (!in_array($data['db_drive'], $drivers))
			{
				throw new \UnexpectedValueException(sprintf('Your input "%s" is not a valid input.  Valid options are: %s', $data['db_drive'], implode(', ', $drivers)));
			}

			$this->app->out('Please enter your database host.  Typically, this will be "localhost".');
			$data['database_host'] = trim($this->app->in());

			$this->app->out('Please enter your database username.');
			$data['database_user'] = trim($this->app->in());

			$this->app->out('Please enter your database user\'s password.');
			$data['database_password'] = trim($this->app->in());

			$this->app->out('Please enter the name of the database to install to.');
			$data['database_name'] = trim($this->app->in());

			$this->app->out('Please enter a database table prefix for your database\'s tables.');
			$data['database_prefix'] = trim($this->app->in());

			$this->app->out('Please enter your administrator user\'s first name.');
			$data['first_name'] = trim($this->app->in());

			$this->app->out('Please enter your administrator user\'s last name.');
			$data['last_name'] = trim($this->app->in());

			$this->app->out('Please enter your administrator user\'s username.');
			$data['username'] = trim($this->app->in());

			$this->app->out('Please enter your administrator user\'s password.');
			$data['password'] = trim($this->app->in());

			$this->app->out('Please enter your administrator user\'s e-mail address.');
			$data['email'] = trim($this->app->in());

			$this->app->out('Please enter your site\'s name.');
			$data['site_name'] = trim($this->app->in());
		}

		$model->install($data);

		if (file_exists(JPATH_CONFIGURATION . '/install_data.txt'))
		{
			File::delete(JPATH_CONFIGURATION . '/install_data.txt');
		}
	}
}
