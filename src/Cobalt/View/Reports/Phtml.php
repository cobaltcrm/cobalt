<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\Reports;

use Joomla\View\AbstractHtmlView;
use Cobalt\Helper\DealHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

//Display partial views
class Phtml extends AbstractHtmlView
{

    public function render()
    {
        $this->deal_sources = DealHelper::getSources();
        return 	parent::render();
     }
}
