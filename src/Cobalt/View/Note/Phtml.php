<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\Note;

use Cobalt\Helper\NoteHelper;
use Joomla\View\AbstractHtmlView;

defined( '_CEXEC' ) or die( 'Restricted access' );

//Display partial views
class Phtml extends AbstractHtmlView
{
    public function render()
    {
        $app = \Cobalt\Container::fetch('app');
        $this->categories = NoteHelper::getCategories();

        return parent::render();
    }
}
