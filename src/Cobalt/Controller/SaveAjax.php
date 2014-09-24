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
        $item_id = $this->input->get('item_id');
        $item_type = $this->input->get('item_type');
        $field = $this->input->get('field');
        $value = $this->input->getString('value');

        $db = $this->container->fetch('db');

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

        $returnRow = true;
        $return = $model->store($data, $returnRow);

        if ($return instanceof AbstractTable) {
            echo json_encode($return->getProperties());
        } else {
            echo json_encode($return);
        }
    }

}
