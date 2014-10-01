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

use Cobalt\Model\Conversation as ConversationModel;
use Cobalt\Helper\ViewHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class GetConvoEntry extends DefaultController
{
    public function execute()
    {
        $convo_id = $this->input->get('convo_id');

        $model = new ConversationModel;
        $convo = $model->getConversation($convo_id);

        $convo_view = ViewHelper::getView('deals','conversation_entry','phtml',array('conversation'=>$convo[0]));
        echo $convo_view->render();
    }

}
