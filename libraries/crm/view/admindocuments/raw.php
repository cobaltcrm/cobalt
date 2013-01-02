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

class CobaltViewAdmindocumentsRaw extends JViewHtml
{
    function render($tpl = null)
    {
    	//authenticate the current user to make sure they are an admin
        CobaltHelperUsers::authenticateAdmin();

        //Add styles for iframe popup
        echo "<link href='".JURI::base()."libraries/crm/media/css/style.css' type='text/css' rel='stylesheet' />";
        echo "<link href='".JURI::base()."libraries/crm/media/css/bootstrap.min.css' type='text/css' rel='stylesheet' />";

        //import document
        if ( is_array($_FILES) && count($_FILES) > 0 ){
        	$model = new CobaltModelDocuments();
        	$model->upload();
        }
        
        //display
        return parent::render();
    }
    
}