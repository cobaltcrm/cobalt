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

use Cobalt\Model\Graphs as GraphsModel;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Graph extends DefaultController
{
    public function execute()
    {
        //get graph data from model
        $model = new GraphsModel;

        $type = $this->getInput()->getCmd('filter');
        if ($type == 'company') {
            $graph_data = $model->getGraphData('company');
        } else {
            $graph_data = $model->getGraphData($type,$this->getInput()->getInt('id'));
        }

        //return data
        echo json_encode($graph_data);
    }

}
