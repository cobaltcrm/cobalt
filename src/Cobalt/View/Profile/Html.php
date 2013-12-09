<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\Profile;

use Joomla\View\AbstractHtmlView;
use JFactory;
use JUri;
use Cobalt\Helper\UsersHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Html extends AbstractHtmlView
{
    public function render()
    {
        //javascript
        $document = JFactory::getDocument();
        $document->addScript( JURI::base().'src/Cobalt/media/js/profile_manager.js' );

        //get user data and pass to view
        $this->user = UsersHelper::getLoggedInUser();
        $this->user_id = UsersHelper::getUserId();

        //display
        return parent::render();
    }

}
