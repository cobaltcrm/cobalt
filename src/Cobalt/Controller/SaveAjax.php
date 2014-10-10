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

use Cobalt\Table\AbstractTable;

class SaveAjax extends DefaultController
{
    public function execute()
    {
        $item_id = $this->getInput()->get('item_id');
        $item_type = $this->getInput()->get('item_type');
        $field = $this->getInput()->get('field');
        $value = $this->getInput()->getString('value');

        $db = $this->container->get('db');

        $data = array('id' => $item_id, $field => $db->escape($value));
        $post_data = $_POST;
        foreach (array('item_id','item_type','field', 'value', 'format', 'tmpl') as $key) {
            if (isset($post_data[$key])) {
                unset($post_data[$key]);
            }
        }
        $post_data = array_filter($post_data);
        $data = array_merge($data, $post_data);

        $modelClass = 'Cobalt\\Model\\' . ucfirst($item_type);

        $model = new $modelClass();

        $response   = new \stdClass;
        $response->alert = new \stdClass;
        $return = $model->store($data, true);
        if ($return instanceof AbstractTable){
            $response->item = $return->getProperties();
        } else {
            $response->item = $return;
        }
        $response->alert->message = TextHelper::_('COBALT_SUCCESSFULLY_SAVED');
        $response->alert->type = 'success';
        echo json_encode($response);
    }

}
