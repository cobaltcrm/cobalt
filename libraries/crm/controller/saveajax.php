<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CobaltControllerSaveAjax extends CobaltControllerDefault
{

    public function execute()
    {
        $app = JFactory::getApplication();

        $item_id = $app->input->get('item_id');
        $item_type = $app->input->get('item_type');
        $field = $app->input->get('field');
        $value = $app->input->get('value');

        $db =& JFactory::getDBO();

        $data = array('id'=>$item_id,$field=>$db->escape($value));
        $post_data = $app->input->getRequest('post');

        $data = array_merge($data,$post_data);

        $modelClass = 'CobaltModel' . ucfirst($item_type);

        $model = new $modelClass();

        $returnRow = TRUE;
        $return = $model->store($data,$returnRow);

        echo json_encode($return);
    }

}
