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

use Cobalt\Router;
use Cobalt\Model\Company as CompanyModel;
use Cobalt\Model\Deal as DealModel;
use Cobalt\Model\People as PeopleModel;
use Cobalt\Model\Conversation as ConversationModel;
use Cobalt\Model\Note as NoteModel;
use Cobalt\Model\Event as EventModel;
use Cobalt\Helper\TextHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Save extends DefaultController
{
    public function execute()
    {
        $modelName = ucwords($this->input->get('model'));
        $modelPath = "Cobalt\\Model\\".$modelName;
        $model = new $modelPath();
        $view = $this->input->get('view');
        $response = new \stdClass;
        $link = $this->input->get('return', Router::to('index.php?view='.$view));

        if ($itemId = $model->store())
        {
            $msg = TextHelper::_('COBALT_SUCCESSFULLY_SAVED');
            $getItem = 'get' . $modelName;

            if ($this->isAjaxRequest())
            {
                $response->item = $model->$getItem($itemId);
                $response->alert = new \stdClass;
                $response->alert->message = $msg;
                $response->alert->type = 'success';
                $this->app->close(json_encode($response));
            }
            else
            {
                $this->app->redirect($link, $msg);
            }
        }
        else
        {
            $msg = TextHelper::_('COBALT_ERROR_SAVING');

            if ($this->isAjaxRequest())
            {
                $this->app->redirect($link, $msg);
            }
            else
            {
                $response->alert = new \stdClass;
                $response->alert->message = $msg;
                $response->alert->type = 'danger';
                $this->app->close(json_encode($response));
            }
        }
    }
}
