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

use Cobalt\Factory;
use Joomla\DI\Container;
use Joomla\DI\ContainerAwareInterface;
use Joomla\Controller\AbstractController;

/**
 * Default controller class for the application
 *
 * @method         \Cobalt\Application  getApplication()  Get the application object.
 * @property-read  \Cobalt\Application  $app              Application object
 *
 * @since          1.0
 */
class DefaultController extends AbstractController implements ContainerAwareInterface
{
	/**
	 * DI Container
	 *
	 * @var    Container
	 * @since  1.0
	 */
	private $container;

    public function execute()
    {
        // Get the document object.
        $viewFormat = $this->getInput()->getWord('format', 'html');
        $viewName   = $this->getInput()->getWord('view', 'dashboard');
        $layoutName = $this->getInput()->getWord('layout', 'default');

	    $view = Factory::getView($viewName, $layoutName, $viewFormat, array('bypass' => false));

        // Render our view.
        echo $view->render();

        return true;
    }

	/**
	 * Get the DI container.
	 *
	 * @return  Container
	 *
	 * @since   1.0
	 * @throws  \UnexpectedValueException May be thrown if the container has not been set.
	 */
	public function getContainer()
	{
		if ($this->container)
		{
			return $this->container;
		}

		throw new \UnexpectedValueException('Container not set in ' . __CLASS__);
	}

    public function isAjaxRequest()
    {
        $headers = apache_request_headers();
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || (isset($headers['X-Requested-With']) && strtolower($headers['X-Requested-With']) === 'xmlhttprequest');
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
		$this->container = $container;

		return $this;
	}
}
