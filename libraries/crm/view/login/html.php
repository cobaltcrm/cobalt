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

class CobaltViewLoginHtml extends JViewHtml
{
    public function render()
    {
        $app = JFactory::getApplication();
        $app->input->set('view','login');
        $app->input->set('layout',$app->input->get('layout','default'));

        return parent::render();
    }

 }
