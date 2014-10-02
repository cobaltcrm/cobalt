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

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

use Cobalt\Helper\TextHelper;
use Cobalt\Model\Note;
use Cobalt\Helper\ViewHelper;

class GetNotes extends DefaultController
{
    public function execute()
    {
        $response = array();

        $start = $this->getInput()->getInt('start', 0);
        $limit = $this->getInput()->getInt('limit', 4);
        $object_id = $this->getInput()->getInt('object_id', 0);
        $item_type = $this->getInput()->getCmd('item_type');

        if ($start < 0) {
            $response['alert'] = array(
                'message' => TextHelper::_('COBALT_ERROR_LIMIT_START_SHOULD_BE_POSITIVE'),
                'type' => 'error'
            );
        }
        else if ($limit < 1) {
            $response['alert'] = array(
                'message' => TextHelper::_('COBALT_ERROR_LIMIT_SHOULD_BE_POSITIVE'),
                'type' => 'error'
            );
        } else if (empty($item_type)) {
            $response['alert'] = array(
                'message' => TextHelper::_('COBALT_ERROR_ITEM_TYPE_MUST_BE_NOT_EMPTY'),
                'type' => 'error'
            );
        }

        //Always object_id 0 return empty notes
        $load_more = false;
        if ($object_id && !empty($item_type)) {
            $model = new Note;
            $notes = $model->getNotes($object_id, $item_type, false);
            $total = count($notes);
            //filter notes by limits
            $items = array_slice($notes,$start, $limit);

            $view = ViewHelper::getView('note','default','phtml', array('notes' => $items));

            $load_more = (($start + $limit) >= $total) ? false : true ;

            $response['html'] = '';
            foreach ($items as $note) {
                $view = ViewHelper::getView('note','entry','phtml',array('note' => $note));
                $response['html'] .= $view->render();
            }
        }

        if ($load_more) {
            $response['loadmore'] = array(
                'limit' => count($items) + $limit
            );
        }

        echo json_encode($response);
    }

}
