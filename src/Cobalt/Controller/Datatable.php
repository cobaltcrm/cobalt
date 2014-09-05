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

class Datatable extends DefaultController
{
    public function execute()
    {
        $loc        = $this->makeSingular($this->input->getString('loc'));
        $modelPath  = "Cobalt\\Model\\".ucwords($loc);
        $model      = new $modelPath();
        $response   = new \stdClass;

        $response->data = $model->getDataTableItems();
        $response->draw = $this->input->getInt('draw');
        $response->recordsTotal = $model->getTotal();
        $response->recordsFiltered = $response->recordsTotal; // @TODO: make this true number

        $alerts = $this->app->getMessageQueue();

        if (isset($alerts[0]))
        {
            $response->alert = new \stdClass;
            $response->alert->message = $alerts[0];
            $response->alert->type = 'alert';
            $this->app->clearMessageQueue();
        }

        $this->app->close(json_encode($response));
    }

    /**
     * Method returns singular of some word.
     * It is quite stupic simple method for 3 words
     * we need to make signular from. Not solwing all world problems.
     * 
     * @param string $name
     * @return string
     */
    protected function makeSingular($name)
    {
        if ($name == 'companies')
        {
            $name = 'company';
        }
        elseif ($name == 'people')
        {
            $name = 'person';
        }
        else
        {
            $lastChar = mb_substr($name, -1);

            if ($lastChar == 's')
            {
                $name = mb_substr($name, 0, -1);
            }
            
        }

        return $name;
    }

    /**
     * Method returns plural of some word.
     * It is quite stupic simple method for 3 words
     * we need to make plural from. Not solwing all world problems.
     * 
     * @param string $name
     * @return string
     */
    protected function makePlural($name)
    {
        if ($name == 'company')
        {
            $name = 'companies';
        }
        elseif ($name == 'person')
        {
            $name = 'people';
        }
        else
        {
            $name = $name . 's';
        }

        return $name;
    }
}
