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
        $app = \Cobalt\Container::fetch('app');

        $document = $app->getDocument();
        $app->input->set('view', $viewName);

        // Register the layout paths for the view
        $paths = new \SplPriorityQueue;

        $themeOverride = JPATH_THEMES . '/' . $app->get('theme') . '/html/' . strtolower($viewName);
        if (is_dir($themeOverride)) {
            $paths->insert($themeOverride, 'normal');
        }

        $paths->insert(JPATH_COBALT . '/View/' . ucfirst($viewName) . '/tmpl', 'normal');

        $viewClass 	= 'Cobalt\\View\\' . ucfirst($viewName) . '\\' . ucfirst($viewFormat);
        $modelClass = ucfirst($viewName);

        if (class_exists('Cobalt\\Model\\'.$modelClass) === false) {
            $modelClass = 'DefaultModel';
        }

        $model = self::getModel($modelClass);

        /** @var $view \Joomla\View\AbstractHtmlView **/
        $view = new $viewClass($model, $paths);
        $view->setLayout($layoutName);
        $view->document = $document;

        if (isset($vars)) {

            $view->bypass = true;

            foreach ($vars as $varName => $var) {
                $view->$varName = $var;
            }

        }

        return $view;
    }

    public static function getModel($modelName)
    {
        $fqcn = 'Cobalt\\Model\\' . $modelName;

        return \Cobalt\Container::getInstance()->buildObject($fqcn);
    }
}
