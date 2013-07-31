<?php

namespace Cobalt\Helper;

use JFactory;

// no direct access
defined('_CEXEC') or die('Restricted access');

class ViewHelper
{
    public static function getView($viewName, $layoutName='default', $viewFormat='html', $vars=null)
    {
        // Get the application
        $app = JFactory::getApplication();

        $app->input->set('view', $viewName);

        // Register the layout paths for the view
        $paths = new \SplPriorityQueue;
        $paths->insert(JPATH_ROOT . '/src/Cobalt/View/' . ucfirst($viewName) . '/tmpl', 'normal');

        $viewClass 	= 'Cobalt\\View\\' . ucfirst($viewName) . '\\' . ucfirst($viewFormat);
        $modelClass = 'Cobalt\\Model\\' . ucfirst($viewName);

        if (false == class_exists($modelClass)) {
            $modelClass = 'Cobalt\\Model\\DefaultModel';
        }

        $view = new $viewClass(new $modelClass, $paths);
        $view->setLayout($layoutName);

        if (isset($vars)) {

            $view->bypass = true;

            foreach ($vars as $varName => $var) {
                $view->$varName = $var;
            }

        }

        return $view;
    }
}
