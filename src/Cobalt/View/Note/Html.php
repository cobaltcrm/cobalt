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
use JFactory;
use JUri;
use Cobalt\Model\Note as NoteModel;
use Cobalt\Helper\NoteHelper;

defined( '_CEXEC' ) or die( 'Restricted access' );

class Html extends AbstractHtmlView
{
    public function render()
    {
        $app = JFactory::getApplication();
        $type = $app->input->get('type');
        $id = $app->input->get('id');
        $view = $app->input->get('view');

        $document = JFactory::getDocument();
        $document->addScript(JURI::base().'src/Cobalt/media/js/cobalt-admin.js');

        //retrieve task list from model
        $model = new NoteModel;

        switch ($view) {
            case "companies":
                $view = "company";
                break;
            case "deals":
                $view = "deal";
                break;
            case "events":
                $view = "event";
                break;
        }

        $notes = $model->getNotes($id,$view,FALSE);
        $this->notes = $notes;
        $this->categories = NoteHelper::getCategories();

        //display
        return parent::render();
    }

}
