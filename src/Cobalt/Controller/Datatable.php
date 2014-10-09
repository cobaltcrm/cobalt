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

use Joomla\Registry\Registry;
use Cobalt\Router;
use Cobalt\Model\Company as CompanyModel;
use Cobalt\Model\Deal as DealModel;
use Cobalt\Model\People as PeopleModel;
use Cobalt\Model\Conversation as ConversationModel;
use Cobalt\Model\Note as NoteModel;
use Cobalt\Model\Event as EventModel;
use Cobalt\Helper\TextHelper;
use Cobalt\Helper\UsersHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Datatable extends DefaultController
{
    public function execute()
    {
        $loc        = $this->getModelName($this->getInput()->getString('loc'));
        $modelPath  = "Cobalt\\Model\\".ucwords($loc);
        $model      = new $modelPath();
        $start      = $this->getInput()->getInt('start', 0);
        $length     = $this->getInput()->getInt('length', 0);
        $orderArr   = $this->getInput()->get('order', array(array('column' => 1, 'dir' => 'asc')), 'ARRAY');
        $searchArr  = $this->getInput()->get('search', array('value' => '', 'regex' => false), 'ARRAY');
        $columns    = $model->getDataTableColumns();
        $user       = $this->getApplication()->getUser();
        $teamId     = UsersHelper::getTeamId();
        $memberRole = UsersHelper::getRole();
        $countFunc  = 'get' . ucwords($loc) . 'Count';

        // Set request variables which models will understand
        if (isset($columns[$orderArr[0]['column']]['ordering']))
        {
            $order  = $columns[$orderArr[0]['column']]['ordering'];
            $this->getInput()->set('filter_order', $order);
        }

        if (isset($orderArr[0]['dir']))
        {
            $dir    = $orderArr[0]['dir'];
            $this->getInput()->set('filter_order_Dir', $dir);
        }

        if (isset($searchArr['value']))
        {
            $value  = $this->setFilters($searchArr['value']);
        }

        $this->getInput()->set('limit', $length);
        $this->getInput()->set('limitstart', $start);

        // Prepare response
        $response   = new \stdClass;

        $response->data = $model->getDataTableItems();
        $response->draw = $this->getInput()->getInt('draw');
        $response->recordsTotal = UsersHelper::$countFunc($user->get('id'), $teamId, $memberRole);
        $response->recordsFiltered = $model->getTotal();

        $alerts = $this->getApplication()->getMessageQueue();

        if (isset($alerts[0]))
        {
            $response->alert = new \stdClass;
            $response->alert->message = $alerts[0];
            $response->alert->type = 'alert';
            $this->getApplication()->clearMessageQueue();
        }

        $this->getApplication()->close(json_encode($response));
    }

    /**
     * Method returns model of item.
     *
     * @param string $name
     * @return string
     */
    protected function getModelName($name)
    {
        $doNotChange = array('stages', 'people', 'users', 'categories', 'sources', 'statuses', 'templates', 'documents');
        
        if (in_array($name, $doNotChange))
        {
            return $name;
        }
        if ($name == 'companies')
        {
            $name = 'company';
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
     * Method parses JSON filters and decides if it's only
     * basic text search, filter applied or combination.
     *
     * @param string $filter
     * @return string
     */
    protected function setFilters($filters)
    {
        $filters = json_decode($filters);

        if ($filters)
        {
            foreach ($filters as $filter => $value)
            {
                // distinquish filter from fulltext search
                if ($filter == 'search')
                {
                    $loc = $this->getModelName($this->getInput()->getString('loc', ''));
                    $this->getInput()->set(strtolower($loc) . '_name', $value);
                }
                else
                {
                    $this->getInput()->set($filter, $value);
                }
            }
        }
    }
}
