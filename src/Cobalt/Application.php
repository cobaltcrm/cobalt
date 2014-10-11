<?php
/**
 * @package    Cobalt.CRM
 *
 * @copyright  Copyright (C) 2012 Cobalt. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Cobalt;

defined('_CEXEC') or die;

use JFactory;

use Joomla\DI\Container;
use Joomla\DI\ContainerAwareInterface;
use Joomla\Registry\Registry;
use Joomla\Language\Language;
use Joomla\Language\Text;
use Joomla\Application\AbstractWebApplication;
use Joomla\Authentication\Authentication;

use Cobalt\Helper\ActivityHelper;
use Cobalt\Helper\DateHelper;
use Cobalt\Helper\RouteHelper;
use Cobalt\Helper\TemplateHelper;
use Cobalt\Helper\UsersHelper;
use Cobalt\Authentication\DatabaseStrategy;
use Cobalt\Authentication\AuthenticationException;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;

/**
 * Cobalt Application class
 *
 * Provide many supporting API functions
 *
 * @package    	Cobalt.CRM
 * @subpackage  Application
 * @since       1.0
 */
final class Application extends AbstractWebApplication implements ContainerAwareInterface
{
	/**
	 * DI Container
	 *
	 * @var    Container
	 * @since  1.0
	 */
	private static $container;

	/**
	 * A session object.
	 *
	 * @var    Session
	 * @since  1.0
	 * @note   This has been created to avoid a conflict with the $session member var from the parent class.
	 */
	private $cSession = null;

	/**
	 * Currently active template
	 *
	 * @var    \stdClass
	 * @since  1.0
	 */
	private $template = null;

	/**
	 * The Application router
	 *
	 * @var    Router
	 * @since  1.0
	 */
	public $router = null;

	/**
	 * The application message queue.
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $messageQueue = array();

	/**
	 * Array of cached User objects
	 *
	 * @var    User[]
	 * @since  1.0
	 */
	protected $users = array();

	/**
	 * The User object.
	 *
	 * @var    User
	 * @since  1.0
	 */
	private $user;

	/**
	 * The Language object
	 *
	 * @var    Language
	 * @since  1.0
	 */
	private $language;

	/**
	 * Application constructor
	 *
	 * @since   1.0
	 */
	public function __construct()
	{
		parent::__construct();

		// Set our upload path URIs
		$this->set('uri.uploads.full', $this->get('uri.base.full') . 'uploads/');
		$this->set('uri.uploads.path', $this->get('uri.base.path') . 'uploads/');

		$container = new Container;

		$container->registerServiceProvider(new Provider\ApplicationServiceProvider($this))
			->registerServiceProvider(new Provider\PhpengineRendererProvider);

		// Setup the application pieces.
		$this->setContainer($container);

		// Load the library language file
		//$this->getLanguage()->load('lib_joomla', JPATH_BASE);

		// TODO - NO MORE JFACTORY
		JFactory::$application = $this;
	}

	/**
	 * Initialize the configuration object.
	 *
	 * @return  $this  Method allows chaining
	 *
	 * @since   1.0
	 * @throws \RuntimeException
	 */
	private function loadConfiguration()
	{
		$config = $this->getContainer()->get('config');

		if ($config === null)
		{
			throw new \RuntimeException(sprintf('Unable to parse the configuration file %s.', $file));
		}

		$this->config->merge($config);

		return $this;
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
			$this->language = Language::getInstance(
				$this->get('language'),
				$this->get('debug_lang')
			);

			// Configure Text to use language instance
			Text::setLanguage($this->language);
		}

		return $this->language;
	}

	/**
	 * Provides a secure hash based on a seed
	 *
	 * @param string $seed Seed string.
	 *
	 * @return string A secure hash
	 *
	 * @since   11.1
	 */
	public static function getHash($seed)
	{
		return md5(Factory::getApplication()->getContainer()->get('config')->get('secret') . $seed);
	}

	/**
	 * Get a session object.
	 *
	 * @return  Session
	 *
	 * @since   1.0
	 */
	public function getSession()
	{
		if (is_null($this->cSession))
		{
			$this->cSession = $this->getContainer()->get('session');
		}

		return $this->cSession;
	}

	/**
	 * Clear session + database session row
	 *
	 * @return boolean
	 *
	 * @since   1.0
	 */
	public function clearSession()
	{
		$this->getSession()->clear();
	}

	/**
	 * Gets the value of a user state variable.
	 *
	 * @param string $key     The key of the user state variable.
	 * @param string $request The name of the variable passed in a request.
	 * @param string $default The default value for the variable if not found. Optional.
	 * @param string $type    Filter for the variable, for valid values see {@link JFilterInput::clean()}. Optional.
	 *
	 * @return The request user state.
	 *
	 * @since   11.1
	 */
	public function getUserStateFromRequest($key, $request, $default = null, $type = 'none')
	{
		$cur_state = $this->getUserState($key, $default);
		$new_state = $this->input->get($request, null, 'default', $type);

		// Save the new value only if it was set in this request.
		if ($new_state !== null) {
			$this->setUserState($key, $new_state);
		} else {
			$new_state = $cur_state;
		}

		return $new_state;
	}

	/**
	 * Gets a user state.
	 *
	 * @param string $key     The path of the state.
	 * @param mixed  $default Optional default value, returned if the internal value is null.
	 *
	 * @return mixed The user state or null.
	 *
	 * @since   1.0
	 */
	public function getUserState($key, $default = null)
	{
		/* @type Registry $registry */
		$registry = $this->getSession()->get('registry');

		if (!is_null($registry)) {
			return $registry->get($key, $default);
		}

		return $default;
	}

	/**
	 * Sets the value of a user state variable.
	 *
	 * @param string $key   The path of the state.
	 * @param string $value The value of the variable.
	 *
	 * @return mixed The previous state, if one existed.
	 *
	 * @since   11.1
	 */
	public function setUserState($key, $value)
	{
		$registry = $this->getSession()->get('registry');

		if (!is_null($registry)) {
			return $registry->set($key, $value);
		}

		return null;
	}

	/**
	 * Method to run the application routines.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function doExecute()
	{
		// Register the template to the config
		$template = $this->getTemplate(true);
		$this->set('theme', $template->template);
		$this->set('themeFile', $this->input->get('tmpl', 'index') . '.php');

		// Install check
		if (!file_exists(JPATH_CONFIGURATION . '/configuration.php')  || (filesize(JPATH_CONFIGURATION . '/configuration.php') < 10))
		{
			// Redirect to the installer if we aren't there
			if (strpos($this->get('uri.route'), 'install') === false && $this->input->getString('task') != 'install')
			{
				ob_end_flush();
				$this->redirect(RouteHelper::_('index.php?view=install'));
			}

			// Build a session object to push into the DI container
			$session = new Session(new MockFileSessionStorage());

			$this->getContainer()->set('session', $session);

			// Fetch the controller
			/** @var \Cobalt\Controller\DefaultController $controllerObj */
			$controllerObj = $this->getRouter()->getController($this->get('uri.route'));
			$controllerObj->setApplication($this)->setContainer($this->getContainer());

			// Perform the Request task
			$controllerObj->execute();
		}
		elseif (file_exists(JPATH_CONFIGURATION . '/configuration.php') && (filesize(JPATH_CONFIGURATION . '/configuration.php') > 10)
		&& strpos($this->get('uri.route'), 'install') !== false)
		{
			$this->redirect(RouteHelper::_('index.php'));
		}
		else
		{
			// Finish bootstrapping the application now
			$this->getContainer()->registerServiceProvider(new Provider\ConfigServiceProvider)
				->registerServiceProvider(new Provider\DatabaseServiceProvider)
		        ->registerServiceProvider(new Provider\SessionServiceProvider);

			$this->loadConfiguration();

			// Load Language
			UsersHelper::loadLanguage();

			// Set site timezone
			$tz = DateHelper::getSiteTimezone();

			// Get user object
			$user = $this->getUser();

			// Fetch the controller
			/** @var \Cobalt\Controller\DefaultController $controllerObj */
			$controllerObj = $this->getRouter()->getController($this->get('uri.route'));
			$controllerObj->setApplication($this)->setContainer($this->getContainer());

			// Require specific controller if requested
			$controller = $this->input->get('controller', 'default');

			// Load user toolbar
			$format    = $this->input->get('format');
			$overrides = array('ajax', 'mail', 'login');
			$loggedIn  = $user->isAuthenticated();

			if ($loggedIn && $format !== 'raw' && !in_array($controller, $overrides))
			{
				ActivityHelper::saveUserLoginHistory();

				// Set a default view if none exists
				$this->input->def('view', 'dashboard');

				// Grab document instance
				$document = $this->getDocument();

				// Start component div wrapper
				if (!in_array($this->input->get('view'), array('print')))
				{
					TemplateHelper::loadToolbar();
				}

				TemplateHelper::startCompWrap();

				// Load javascript language
				TemplateHelper::loadJavascriptLanguage();

				TemplateHelper::showMessages();
			}

			if (!$loggedIn && !($controllerObj instanceof \Cobalt\Controller\Login))
			{
				$this->redirect(RouteHelper::_('index.php?view=login'));
			}

			// Fullscreen detection
			if (UsersHelper::isFullscreen())
			{
				$this->input->set('tmpl', 'component');
			}

			// Perform the Request task
			$controllerObj->execute();

			// End componenet wrapper
			if ($user !== false && $format !== 'raw')
			{
				TemplateHelper::endCompWrap();
			}
		}
	}

	/**
	 * Logs the user into the application
	 *
	 * @return  void  Redirects the application
	 *
	 * @since   1.0
	 * @throws  AuthenticationException
	 */
	public function login()
	{
		// Get the Authentication object
		$authentication = new Authentication;

		// Add our authentication strategy
		$strategy = new DatabaseStrategy($this->input, $this->getContainer()->get('db'));
		$authentication->addStrategy('database', $strategy);

		// Authenticate the user
		$authentication->authenticate(array('database'));

		switch ($strategy->getResult())
		{
			case Authentication::NO_CREDENTIALS :
				throw new AuthenticationException('A username and/or password were not provided.');

			case Authentication::NO_SUCH_USER :
				throw new AuthenticationException('The username provided does not exist.');

			case Authentication::INVALID_CREDENTIALS :
				throw new AuthenticationException('The username and/or password is incorrect.');

			case Authentication::SUCCESS :
				$user = $this->getUser();
				$user->loadByUsername($this->input->{$this->input->getMethod()}->get('username', false, 'username'));

				// Set the authenticated user in the session and redirect to the manager
				$this->setUser($user)->redirect(RouteHelper::_('index.php?view=dashboard'));
		}
	}

	/**
	 * Get the template data
	 *
	 * @param   boolean  $params  True to return a stdClass object with the full template data, false to return the template name only
	 *
	 * @return  \stdClass|string  The template name
	 *
	 * @since   1.0
	 */
	public function getTemplate($params = false)
	{
		if (is_object($this->template))
		{
			if ($params)
			{
				return $this->template;
			}

			return $this->template->template;
		}

		// Fallback template
		$template = new \stdClass;

		$template->template = 'bootstrap';
		if (!file_exists(JPATH_THEMES . '/default/index.php'))
		{
			$template->template = '';
		}

		$template->file      = $this->input->get('tmpl', 'index') . '.php';
		$template->directory = 'themes';

		$this->template = $template;
		if ($params)
		{
			return $template;
		}

		return $template->template;
	}

	/**
	 * Overrides the default template that would be used
	 *
	 * @param   string  $template     The template name
	 * @param   mixed   $styleParams  The template style parameters
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function setTemplate($template, $styleParams = null)
	{
		if (is_dir(JPATH_THEMES . '/' . $template))
		{
			$this->template           = new \stdClass;
			$this->template->template = $template;

			if ($styleParams instanceof Registry)
			{
				$this->template->params = $styleParams;
			}
			else
			{
				$this->template->params = new Registry($styleParams);
			}
		}
	}

	/**
	 * Allows the application to load a custom or default router.
	 *
	 * @return  Router
	 *
	 * @since   1.0
	 */
	public function getRouter()
	{
		if (is_null($this->router))
		{
			$this->router = new Router($this->input);

			$maps = json_decode(file_get_contents(JPATH_ROOT . '/src/routes.json'));

			if (!$maps)
			{
				throw new \RuntimeException('Invalid router file.');
			}

			$this->router->addMaps($maps, true);
			$this->router->setDefaultController('Cobalt\\Controller\\DefaultController');
		}

		return $this->router;
	}

	/**
	 * Redirect to another URL.
	 *
	 * If the headers have not been sent the redirect will be accomplished using a "301 Moved Permanently"
	 * or "303 See Other" code in the header pointing to the new location. If the headers have already been
	 * sent this will be accomplished using a JavaScript statement.
	 *
	 * @param   string   $url      The URL to redirect to. Can only be http/https URL
	 * @param   string   $msg      An optional message to display on redirect.
	 * @param   string   $msgType  An optional message type.
	 * @param   boolean  $moved    True if the page is 301 Permanently Moved, otherwise 303 See Other is assumed.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function redirect($url, $msg = '', $msgType = 'message', $moved = false)
	{
		if ($msg)
		{
			$this->enqueueMessage($msg, $msgType);
		}

		parent::redirect($url, $moved);
	}

	/**
	 * Enqueue a system message.
	 *
	 * @param string $msg  The message to enqueue.
	 * @param string $type The message type. Default is message.
	 *
	 * @return  $this  Method allows chaining
	 *
	 * @since   1.0
	 */
	public function enqueueMessage($msg, $type = 'message')
	{
		$this->getSession()->getFlashBag()->add($type, $msg);

		return $this;
	}

	/**
	 * Clear the system message queue.
	 *
	 * @return void
	 *
	 * @since   1.0
	 */
	public function clearMessageQueue()
	{
		$this->getSession()->getFlashBag()->clear();
	}

	/**
	 * Get the system message queue.
	 *
	 * @return array The system message queue.
	 *
	 * @since   1.0
	 */
	public function getMessageQueue()
	{
		return $this->getSession()->getFlashBag()->peekAll();
	}

	/**
	 * Set the system message queue for a given type.
	 *
	 * @param string $type    The type of message to set
	 * @param mixed  $message Either a single message or an array of messages
	 *
	 * @return void
	 *
	 * @since   1.0
	 */
	public function setMessageQueue($type, $message = '')
	{
		$this->getSession()->getFlashBag()->set($type, $message);
	}

	/**
	 * Login or logout a user.
	 *
	 * @param   User|null  $user  The User object or null to set a guest user.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function setUser(User $user = null)
	{
		$this->getSession()->set('cobalt_user', $user);

		return $this;
	}

	/**
	 * Get a user object.
	 *
	 * @param   integer  $id  The user id or the current user.
	 *
	 * @return  User
	 *
	 * @since   1.0
	 */
	public function getUser($id = 0)
	{
		// Check if user isn't already loaded in chache array
		if ($id && isset($this->users[$id]))
		{
			return $this->users[$id];
		}

		if ($id)
		{
			return new User($this->getContainer()->get('db'), $id);
		}

		if (is_null($this->user))
		{
			if ($this->user = $this->getSession()->get('cobalt_user'))
			{
				$this->user->setDatabase($this->getContainer()->get('db'));
			}
			else
			{
				$this->user = new User($this->getContainer()->get('db'));
			}
		}

		// If User ID is known, store it to cache array
		if ($this->user->id)
		{
			$this->users[$id] = $this->user;
		}

		return $this->user;
	}

	/**
	 * When user changes, refresh his/her data from database
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	public function refreshUser()
	{
		$user = $this->getUser();

		if (!$user->get('id'))
		{
			// user must be logged in to be able to refresh itself
			return false;
		}

		$user->load($user->get('id'));
		$this->setUser($user);

		return true;
	}

	protected function detectRequestUri()
	{
		return str_replace('index.php', '', parent::detectRequestUri());
	}
}
