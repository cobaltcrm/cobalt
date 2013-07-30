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

class CobaltControllerCancel extends CobaltControllerDefault
{
    public function execute()
    {
        $app = JFactory::getApplication();
        $view = $app->input->get('view');

        $msg = JText::_('Entry cancelled!');
        $app->redirect('index.php?view='.$view,$msg);
    }
}
