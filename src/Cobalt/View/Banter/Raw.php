<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\Banter;

use Joomla\View\AbstractHtmlView;;
use Cobalt\Helper\TranscriptlistsHelper;

defined( '_CEXEC' ) or die( 'Restricted access' );

class Raw extends AbstractHtmlView
{
    public function render()
    {
        $layout = $this->getLayout();

        switch ($layout) {
            case "transcripts":
                $this->transcripts = TranscriptlistsHelper::getTranscripts();
            break;
        }

        //display
        echo parent::render();

    }

}
