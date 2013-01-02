<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 

class CobaltControllerDefault extends JControllerBase
{

	public function execute()
	{

		// Get the application
		$app = $this->getApplication();

		// Get the document object.
		$document 		= $app->getDocument();

		$viewName   	= $app->input->getWord('view', 'dashboard');
		$viewFormat		= $document->getType();
		$layoutName   	= $app->input->getWord('layout', 'default');

		$app->input->set('view', $viewName);

        // Register the layout paths for the view
		$paths = new SplPriorityQueue;
		$paths->insert(JPATH_COBALT . '/view/' . $viewName . '/tmpl', 'normal');

		$viewClass 	= 'CobaltView' . ucfirst($viewName) . ucfirst($viewFormat);
		$modelClass = 'CobaltModel' . ucfirst($viewName);

		if (false === class_exists($modelClass))
		{
			$modelClass = 'CobaltModelDefault';
		}

		$view = new $viewClass(new $modelClass, $paths);
		$view->setLayout($layoutName);

		// Render our view.
		echo $view->render();

		return true;
	}
}