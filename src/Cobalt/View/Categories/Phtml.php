<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\Categories;

use Cobalt\Helper\UsersHelper;
use Joomla\View\AbstractHtmlView;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

//Display partial views
class Phtml extends AbstractHtmlView
{

    public function render()
    {
        //authenticate the current user to make sure they are an admin
        UsersHelper::authenticateAdmin();

        return parent::render();
     }
}
