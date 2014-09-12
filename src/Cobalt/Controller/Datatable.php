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
        $loc        = $this->makeSingular($this->input->getString('loc'));
        $modelPath  = "Cobalt\\Model\\".ucwords($loc);
        $model      = new $modelPath();
        $start      = $this->input->getInt('start', 0);
        $length     = $this->input->getInt('length', 0);
        $orderArr   = $this->input->get('order', array(array('column' => 1, 'dir' => 'asc')), 'ARRAY');
        $searchArr  = $this->input->get('search', array('value' => '', 'regex' => false), 'ARRAY');
        $columns    = $model->getDataTableColumns();
        $user       = $this->app->getUser();
        $teamId     = UsersHelper::getTeamId();
        $memberRole = UsersHelper::getRole();
        $countFunc  = 'get' . ucwords($loc) . 'Count';

        // Set request variables which models will understand
        if (isset($columns[$orderArr[0]['column']]['ordering']))
        {
            $order  = $columns[$orderArr[0]['column']]['ordering'];
            $this->input->set('filter_order', $order);
        }
        
        if (isset($orderArr[0]['dir']))
        {
            $dir    = $orderArr[0]['dir'];
            $this->input->set('filter_order_Dir', $dir);
        }

        if (isset($searchArr['value']))
        {
            $value    = $this->setFilters($searchArr['value']);
        }

        $this->input->set('limit', $length);
        $this->input->set('limitstart', $start);

        // Prepare response
        $response   = new \stdClass;

        $response->data = $model->getDataTableItems();
        $response->draw = $this->input->getInt('draw');
        $response->recordsTotal = UsersHelper::$countFunc($user->get('id'), $teamId, $memberRole);
        $response->recordsFiltered = $model->getTotal();

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
                    $loc = $this->makeSingular($this->input->getString('loc'));
                    $this->input->set(strtolower($loc) . '_name', $value);
                }
                else
                {
                    $this->input->set($filter, $value);
                }
            }
        }
    }

    protected function setSearch($value)
    {
        
    }

    protected function setFilter($filter)
    {
        if (is_array($filter))
        {
            $layout = $this->input->getString('loc', '');
            $loc    = $this->makeSingular($layout);

            if (count($filter) == 2)
            {
                $column = $filter[0];
                $value  = $filter[1];
            }
            elseif (count($filter) == 1)
            {
                $column = 'item';
                $value  = $filter[0];
            }

            $this->input->set($column, $value);
        }
    }
}
