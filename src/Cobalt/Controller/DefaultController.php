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

/**
 * Default controller class for the application
 *
 * @method         \Cobalt\Application  getApplication()  Get the application object.
 * @property-read  \Cobalt\Application  $app              Application object
 *
 * @since          1.0
 */
class DefaultController extends AbstractController
{
    /**
     * @var Container
     */
    protected $container;

	/**
	 * Instantiate the controller.
	 *
	 * @param   Input                $input  The input object.
	 * @param   AbstractApplication  $app    The application object.
	 *
	 * @since   1.0
	 */
    public function __construct(Input $input = null, AbstractApplication $app = null)
    {
        parent::__construct($input, $app);

	    $this->container = Container::getInstance();
    }

    public function execute()
    {
        // Get the document object.
        $document   = $this->getApplication()->getDocument();
        $viewFormat = $this->getInput()->getWord('format', 'html');
        $viewName   = $this->getInput()->getWord('view', 'dashboard');
        $layoutName = $this->getInput()->getWord('layout', 'default');

        $this->getInput()->set('view', $viewName);

        // Register the layout paths for the view
        $paths = new \SplPriorityQueue;

        $themeOverride = JPATH_THEMES . '/' . $this->getApplication()->get('theme') . '/html/' . strtolower($viewName);
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
        $view->document = $document;

        // Render our view.
        echo $view->render();

        return true;
    }

    public function getModel($modelName)
    {
        $fqcn = 'Cobalt\\Model\\' . $modelName;

        return $this->container->buildObject($fqcn);
    }

    public function isAjaxRequest()
    {
        $headers = apache_request_headers();
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || (isset($headers['X-Requested-With']) && strtolower($headers['X-Requested-With']) === 'xmlhttprequest');
    }

    /**
     * Return Application
     *
     * @return AbstractApplication|mixed
     */
    public function getApplication()
    {
        return $this->container->fetch('app');
    }
}
