<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

class CobaltHelperView
{
	public static function getView($viewName, $layoutName='default', $viewFormat='html', $vars=null)
	{
		// Get the application
		$app = JFactory::getApplication();

		// Get the document object.
		$document 		= $app->getDocument();

		$app->input->set('view', $viewName);

        // Register the layout paths for the view
		$paths = new SplPriorityQueue;
		$paths->insert(JPATH_COBALT . '/view/' . $viewName . '/tmpl', 'normal');

		$viewClass 	= 'CobaltView' . ucfirst($viewName) . ucfirst($viewFormat);
		$modelClass = 'CobaltModel' . ucfirst($viewName);

		if (false == class_exists($modelClass))
		{
			$modelClass = 'CobaltModelDefault';
		}

		$view = new $viewClass(new $modelClass, $paths);
		$view->setLayout($layoutName);

		if(isset($vars)) {

			$view->bypass = true;

			foreach($vars as $varName => $var) {
				$view->$varName = $var;
			}

		}


		return $view;

	}
}