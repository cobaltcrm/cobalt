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
defined( '_CEXEC' ) or die( 'Restricted access' );

class CobaltViewProfileHtml extends JViewHtml
{
    public function render()
    {
        //javascript
        $document = JFactory::getDocument();
        $document->addScript( JURI::base().'libraries/crm/media/js/profile_manager.js' );

        //get user data and pass to view
        $this->user = UsersHelper::getLoggedInUser();
        $this->user_id = UsersHelper::getUserId();

        //display
        return parent::render();
    }

}