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

use Cobalt\Container;
use Joomla\Input\Input;
use Joomla\Application\AbstractApplication;
use Joomla\Controller\AbstractController;

class DefaultController extends AbstractController
{
    /**
     * @var \Cobalt\Container
     */
    protected $container;

    /**
     * @var \Cobalt\Application
     */
    protected $app;

    /**
     * @var \Joomla\Input\Input
     */
    protected $input;

    /**
     * Override the parent so that we can get at the application and input objects properties.
     *
     * @param Input               $input
     * @param AbstractApplication $app
     */
    public function __construct(Input $input = null, AbstractApplication $app = null)
    {
        $this->container = Container::getInstance();
        $this->input = $input;
        $this->app = $app;

        parent::__construct($input, $app);
    }

    public function execute()
    {
        // Get the document object.
        $document   = $this->app->getDocument();
        $viewFormat = $document->getType();
        $viewName   = $this->input->getWord('view', 'dashboard');
        $layoutName = $this->input->getWord('layout', 'default');

        $this->input->set('view', $viewName);

        // Register the layout paths for the view
        $paths = new \SplPriorityQueue;

        $themeOverride = JPATH_THEMES . '/' . $this->app->get('theme') . '/html/' . strtolower($viewName);
        if (is_dir($themeOverride)) {
            $paths->insert($themeOverride, 'normal');
        }

        $paths->insert(JPATH_COBALT . '/View/' . ucfirst($viewName) . '/tmpl', 'normal');

        $viewClass 	= 'Cobalt\\View\\' . ucfirst($viewName) . '\\' . ucfirst($viewFormat);
        $modelClass = ucfirst($viewName);

        if (class_exists('Cobalt\\Model\\'.$modelClass) === false) {
            $modelClass = 'DefaultModel';
        }

        $model = $this->getModel($modelClass);

        /** @var $view \Joomla\View\AbstractHtmlView **/
        $view = new $viewClass($model, $paths);
        $view->setLayout($layoutName);

        // Render our view.
        echo $view->render();

        return true;
    }

    public function getModel($modelName)
    {
        $fqcn = 'Cobalt\\Model\\' . $modelName;

        return $this->container->build($fqcn);
    }
}
