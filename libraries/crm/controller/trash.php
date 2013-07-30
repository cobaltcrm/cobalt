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

class CobaltControllerTrash extends CobaltControllerDefault
{

    public function execute()
    {
        $app = JFactory::getApplication();

        $item_id = $app->input->get('item_id',null,'array');
        $item_type = $app->input->get('item_type');

        //ADD TO MODELS * trash model *
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->update("#__".$item_type)->set("published=-1");
            if ( is_array($item_id) ) {
                $query->where("id IN(".implode(',',$item_id).")");
            } else {
                $query->where("id=".$item_id);
            }
        $db->setQuery($query);
        if ( $db->query() ) {
            $data['success'] = true;
        } else {
            $data['success'] = false;
            $data['error_msg'] = $db->errorMsg;
        }

        $redirect = $app->input->get('page_redirect');
        if ($redirect) {
            $msg = ( $data['success'] ) ? CRMText::_('COBALT_SUCCESSULLY_REMOVED_ITEM') : CRMText::_('COBALT_ERROR_REMOVING_ITEM');
            $app->redirect(JRoute::_('index.php?view='.$redirect),$msg);
        } else {
            echo json_encode($data);
        }
    }

}
