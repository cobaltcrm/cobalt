<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\Controller;

use Cobalt\Model\Note as NoteModel;
use Cobalt\Helper\ViewHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class GetNoteEntry extends DefaultController
{
    public function execute()
    {
        $note_id = $this->input->get('note_id');

        $model = new NoteModel;
        $note = $model->getNote($note_id);

        $note_view = ViewHelper::getView('note','entry','phtml',array('note'=>$note[0]));
        echo $note_view->render();
    }
}
