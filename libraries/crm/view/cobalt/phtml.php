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

//Display partial views
class CobaltViewCobaltPhtml extends JViewHTML
{

    public function render()
    {
        //authenticate the current user to make sure they are an admin
        CobaltHelperUsers::authenticateAdmin();

        return parent::render();
     }
}
