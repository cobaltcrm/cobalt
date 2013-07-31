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

use JFactory;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class SaveAjax extends DefaultController
{

    public function execute()
    {
        $app = JFactory::getApplication();

        $item_id = $app->input->get('item_id');
        $item_type = $app->input->get('item_type');
        $field = $app->input->get('field');
        $value = $app->input->get('value');

        $db = JFactory::getDBO();

        $data = array('id'=>$item_id,$field=>$db->escape($value));
        $post_data = $app->input->getRequest('post');

        $data = array_merge($data,$post_data);

        $modelClass = 'Cobalt\\Model\\' . ucfirst($item_type);

        $model = new $modelClass();

        $returnRow = TRUE;
        $return = $model->store($data,$returnRow);

        echo json_encode($return);
    }

}
