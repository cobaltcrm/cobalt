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
        $modelName  = ucwords($this->getInput()->get('model'));
        $modelPath  = "Cobalt\\Model\\".$modelName;
        $model      = new $modelPath();
        $view       = $this->getInput()->get('view');
        $response   = new \stdClass;
        $link       = $this->getInput()->get('return', Router::to('index.php?view=' . $view));

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

                // just send reload page if send refresh_page=1
                if ($this->getInput()->getInt('refresh_page', 0)) {
                    $response->reload = '3000';
                }

                $this->getApplication()->close(json_encode($response));
            }
            else
            {
                $this->getApplication()->redirect($link, $msg);
            }
        }
        else
        {
            $msg = TextHelper::_('COBALT_ERROR_SAVING');

            if ($this->isAjaxRequest())
            {
                $response->alert = new \stdClass;
                $response->alert->message = $msg;
                $response->alert->type = 'danger';
                $this->getApplication()->close(json_encode($response));
            }
            else
            {
                $this->getApplication()->redirect($link, $msg);

            }
        }
    }
}
