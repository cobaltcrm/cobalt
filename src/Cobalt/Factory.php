<?php
/**
 * Cobalt CRM
 *
 * @copyright  Copyright (C) 2012 - 2014 cobaltcrm.org All Rights Reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Cobalt;

use Joomla\Model\ModelInterface;

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
	 * Fetches a model object
	 *
	 * @param   string  $model  The model to retrieve
	 *
	 * @return  \Cobalt\Model\DefaultModel
	 *
	 * @since   1.0
	 */
	public static function getModel($model)
	{
		return self::getContainer()->buildObject('\\Cobalt\\Model\\' . $model);
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

	/**
	 * Fetches a view object
	 *
	 * @param   string          $viewName    The view name to load
	 * @param   string          $layoutName  The view layout to load
	 * @param   string          $viewFormat  The view format to load
	 * @param   array           $vars        Optional class variables to load into the view
	 * @param   ModelInterface  $model       Optional model to inject into the view
	 *
	 * @return  \Joomla\View\ViewInterface
	 *
	 * @since   1.0
	 */
	public static function getView($viewName, $layoutName = 'default', $viewFormat = 'html', $vars = array(), ModelInterface $model = null)
	{
		// Get the application
		$app = Factory::getApplication();

		$document = $app->getDocument();
		$app->input->set('view', $viewName);

		// Register the layout paths for the view
		$paths = new \SplPriorityQueue;

		$themeOverride = JPATH_THEMES . '/' . $app->get('theme') . '/html/' . strtolower($viewName);

		if (is_dir($themeOverride))
		{
			$paths->insert($themeOverride, 'normal');
		}

		$paths->insert(JPATH_COBALT . '/View/' . ucfirst($viewName) . '/tmpl', 'normal');

		$viewClass  = 'Cobalt\\View\\' . ucfirst($viewName) . '\\' . ucfirst($viewFormat);
		$modelClass = ucfirst($viewName);

		if (class_exists('Cobalt\\Model\\' . $modelClass) === false)
		{
			$modelClass = 'DefaultModel';
		}

		$model = is_null($model) ? Factory::getModel($modelClass) : $model;

		$view = new $viewClass($model, $paths);
		$view->setLayout($layoutName);
		$view->document = $document;

		if (isset($vars))
		{
			foreach ($vars as $varName => $var)
			{
				$view->$varName = $var;
			}
		}

		if (!isset($view->bypass))
		{
			$view->bypass = true;
		}

		return $view;
	}
}
