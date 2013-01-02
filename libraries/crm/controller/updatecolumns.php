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

class CobaltControllerUpdateColumns extends CobaltControllerDefault
{

   function execute(){

	   $app = JFactory::getApplication();
       
       //get the location of the page
       $loc = $app->input->get('loc');
       
       //get new data to insert into user tables
       $column = $app->input->get('column');
       
       //get model
       $model = new CobaltModelUser();
       $model->updateColumns($loc,$column); 
       
   }

}