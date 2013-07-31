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

class CobaltViewAcymailingRaw extends JViewHtml
{
    public function render($tpl = null)
    {
        $layout = $this->getLayout();

        switch ($layout) {
            case "manage":
                $this->lists = CobaltHelperMailinglists::getMailingLists(TRUE);
            break;
            case "links":
                $this->links = CobaltHelperMailinglists::getLinks();
            break;
        }

        //display
        echo parent::render($tpl);
    }

}
