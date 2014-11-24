<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\Model;

use Cobalt\Helper\DateHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Template extends DefaultModel
{
    /**
     * Run items through template system
     */
    public function createTemplate()
    {
        $template_id = $this->app->input->get('template_id');
        $association_id = $this->app->input->get('association_id');
        $association_type = $this->app->input->get('association_type');

        $template = $this->getTemplate($template_id);

        $current_date = date("Y-m-d 00:00:00");

        if (count($template) > 0)
        {
            $event_model = new Event;

            foreach ($template as $event)
            {
                unset($event['id']);

                $event['association_id'] = $association_id;
                $event['association_type'] = $association_type;
                $event['type'] = "task";

                $event['due_date'] = DateHelper::formatDBDate(date("Y-m-d",strtotime($current_date." +".$event['day']." days")),false);
                $event['due_date_hour'] = "00:00:00";

                if (!$event_model->store($event))
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get template events
     * @param  [type] $template_id [description]
     * @return [type] [description]
     */
    public function getTemplate($template_id = null)
    {
        $template_id = $template_id ? $template_id : $this->id;

        $query = $this->db->getQuery(true);

        $query->select("t.*")
            ->from("#__template_data AS t")
            ->where("t.template_id=".$template_id);

        $this->db->setQuery($query);
        $events = $this->db->loadAssocList();

        return $events;
    }

}
