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

use RouteHelper;
use Cobalt\Helper\TextHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Trash extends DefaultController
{
    public function execute()
    {
        $item_id = $this->input->get('item_id',null,'array');
        $item_type = $this->input->get('item_type');

        //ADD TO MODELS * trash model *
        $db = $this->container->resolve('db');
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
            $data['error_msg'] = $db->getErrorMsg();
        }

        $redirect = $this->input->get('page_redirect');
        if ($redirect) {
            $msg = ( $data['success'] ) ? TextHelper::_('COBALT_SUCCESSULLY_REMOVED_ITEM') : TextHelper::_('COBALT_ERROR_REMOVING_ITEM');
            $this->app->redirect(RouteHelper::_('index.php?view='.$redirect),$msg);
        } else {
            echo json_encode($data);
        }
    }

}
