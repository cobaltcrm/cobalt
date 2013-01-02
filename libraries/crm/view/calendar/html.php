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

class CobaltViewCalendarHtml extends JViewHtml
{
	function render()
	{
		//load model and retrieve events to pass to calendar
		$model = new CobaltModelEvent();
		$events = $model->getEvents('calendar');
		
		//load js libs
		$document =& JFactory::getDocument();
		$document->addScript( JURI::base().'libraries/crm/media/js/fullcalendar.js' );
		$document->addScript( JURI::base().'libraries/crm/media/js/calendar_manager.js' );
		
		//load required css for calendar
		$document->addStyleSheet( JURI::base().'libraries/crm/media/css/fullcalendar.css' );
		
		//pass reference vars to view
		$this->events = json_encode($events);
		$team_members = CobaltHelperUsers::getUsers();
		$this->team_members = $team_members;
		
		//display
		return parent::render();
	}
	
}