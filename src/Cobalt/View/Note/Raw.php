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

use Joomla\View\AbstractHtmlView;
use Cobalt\Model\Note as NoteModel;
use Cobalt\Helper\NoteHelper;
use Cobalt\Factory;

defined( '_CEXEC' ) or die( 'Restricted access' );

class Raw extends AbstractHtmlView
{
    public function render($tpl = null)
    {
        $app = Factory::getApplication();

        $this->type = $app->input->getCmd('type');
        $this->id = $app->input->getInt('id');
        $layout = $this->getLayout();
        $this->format = $app->input->get('format');
        $this->view = $app->input->get('view','default');

        switch ($this->type) {
            case 'event':
                $this->var = 'event_id';
                break;
        }

        //retrieve task list from model
        $model = new NoteModel;

        if ($layout == "edit") {
           $notes = $model->getNote($this->id);
        } else {
           $notes = $model->getNotes($this->id,$this->type, false);
        }

        $this->notes = $notes;
        $this->categories = NoteHelper::getCategories();

        //display
        echo parent::render();
    }

}
