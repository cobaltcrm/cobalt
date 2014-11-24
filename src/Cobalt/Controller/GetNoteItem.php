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

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class GetNoteItem extends DefaultController
{
    public function execute()
    {
        $note_id = $this->getInput()->get('note_id');

        $model = new NoteModel;
        $item = $model->getNote($note_id);

        $item[0]['note_id'] = $note_id;

        $response = array(
            'item' => $item[0],
            'modal' => array(
                'action' => 'show'
            )
        );
        echo json_encode($response);
    }
}
