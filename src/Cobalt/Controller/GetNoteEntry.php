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

use Cobalt\Factory;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class GetNoteEntry extends DefaultController
{
    public function execute()
    {
        $note_id = $this->getInput()->get('note_id');

        $model = Factory::getModel('Note');
        $note = $model->getNote($note_id);

        $note_view = Factory::getView('note','entry','phtml',array('note'=>$note[0]), $model);
        echo $note_view->render();
    }
}
