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

class CobaltViewBanterRaw extends JViewHtml
{
    public function render($tpl = null)
    {

        $layout = $this->getLayout();

        switch ($layout) {
            case "transcripts":
                $this->transcripts = CobaltHelperTranscriptlists::getTranscripts();
            break;
        }

        //display
        echo parent::render();

    }

}
