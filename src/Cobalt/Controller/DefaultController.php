<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\Controller;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

use Joomla\Controller\AbstractController;

class DefaultController extends AbstractController
{
    public function execute()
    {
        // Get the application
        $app = $this->getApplication();

        // Get the document object.
        $document   = $app->getDocument();
        $viewName   = $app->input->getWord('view', 'dashboard');
        $viewFormat = $document->getType();
        $layoutName = $app->input->getWord('layout', 'default');

        $app->input->set('view', $viewName);

        // Register the layout paths for the view
        $paths = new \SplPriorityQueue;

        $themeOverride = JPATH_THEMES . '/' . $app->get('theme') . '/html/' . strtolower($viewName);
        if (is_dir($themeOverride)) {
            $paths->insert($themeOverride, 'normal');
        }

        $paths->insert(JPATH_COBALT . '/View/' . ucfirst($viewName) . '/tmpl', 'normal');

        $viewClass 	= 'Cobalt\\View\\' . ucfirst($viewName) . '\\' . ucfirst($viewFormat);
        $modelClass = 'Cobalt\\Model\\' . ucfirst($viewName);

        if (false === class_exists($modelClass)) {
            $modelClass = 'Cobalt\\Model\\DefaultModel';
        }

        /** @var $view \Joomla\View\AbstractHtmlView **/
        $view = new $viewClass(new $modelClass, $paths);
        $view->setLayout($layoutName);

        // Render our view.
        echo $view->render();

        return true;
    }
}
