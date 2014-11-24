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

class GetConvoEntry extends DefaultController
{
    public function execute()
    {
        $convo_id = $this->getInput()->get('convo_id');

        $model = Factory::getModel('Conversation');
        $convo = $model->getConversation($convo_id);

        $convo_view = Factory::getView('deals','conversation_entry','phtml',array('conversation'=>$convo[0]), $model);
        echo $convo_view->render();
    }
}
