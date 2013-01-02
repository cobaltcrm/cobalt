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

class CobaltViewAcymailingHtml extends JViewHtml
{
	function render($tpl = null)
	{

		$this->mailing_lists = CobaltHelperMailinglists::getMailingLists();
		$this->newsletters = CobaltHelperMailinglists::getNewsletters();
	
		//display
		return parent::render();
	}
	
}