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

class CobaltViewNoteHtml extends JViewHtml
{
	function render()
	{

      $app = JFactory::getApplication();
  		$type = $app->input->get('type');
  		$id = $app->input->get('id');
  		$view = $app->input->get('view');

      $document = JFactory::getDocument();
      $document->addScript(JURI::base().'libraries/crm/media/js/cobalt-admin.js');

      //retrieve task list from model
      $model = new CobaltModelNote();

      switch ( $view ){
        case "companies":
            $view = "company";
        break;
        case "deals":
            $view = "deal";
        break;
        case "events":
            $view = "event";
        break;
      }

     	$notes = $model->getNotes($id,$view,FALSE);
     	$this->notes = $notes;
       
    	//display
     	return parent::render();
	}
	
}