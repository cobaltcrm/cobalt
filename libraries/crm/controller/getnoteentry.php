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

class CobaltControllerGetNoteEntry extends CobaltControllerDefault
{
   function execute(){

   		$app = JFactory::getApplication();

        $note_id = $app->input->get('note_id');

        $model = new CobaltModelNote();
        $note = $model->getNote($note_id);

        $note_view = CobaltHelperView::getView('note','entry','phtml',array('note'=>$note[0]));
        echo $note_view->render();

   }
}