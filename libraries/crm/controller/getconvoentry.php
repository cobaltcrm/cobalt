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

class CobaltControllerGetConvoEntry extends CobaltControllerDefault
{
     function execute()
     {
         $app = JFactory::getApplication();

        $convo_id = $app->input->get('convo_id');

        $model = new CobaltModelConversation();
        $convo = $model->getConversation($convo_id);

        $convo_view = CobaltHelperView::getView('deals','conversation_entry','phtml',array('conversation'=>$convo[0]));
        echo $convo_view->render();

   }

}
