<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\Install;

use Cobalt\Factory;
use Joomla\View\AbstractHtmlView;

// no direct access
defined('_CEXEC') or die;

/**
 * HTML View class for the installer
 *
 * @since  1.0
 */
class Html extends AbstractHtmlView
{
	/**
	 * The model object.
	 *
	 * @var    \Cobalt\Model\Install
	 * @since  1.0
	 */
	protected $model;

	/**
	 * Method to render the view.
	 *
	 * @return  string  The rendered view.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function render()
	{
		$this->basepath    = Factory::getApplication()->get('uri.base.host');
		$this->knownLangs  = Factory::getApplication()->getLanguage()->getKnownLanguages();
		$this->defaultLang = Factory::getApplication()->getLanguage()->getDefault();
		$this->phpOptions  = $this->model->getPhpOptions();
		$this->dboDrivers  = $this->model->dboDrivers();

		return parent::render();
	}
}
