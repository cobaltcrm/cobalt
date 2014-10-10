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

use Cobalt\Helper\RouteHelper;
use Cobalt\Helper\TextHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Trash extends DefaultController
{
    public function execute()
    {
        $item_id    = $this->getInput()->get('item_id', null, 'array');
        $item_type  = $this->getInput()->get('item_type');

        //ADD TO MODELS * trash model *
        $db         = $this->getContainer()->get('db');
        $query      = $db->getQuery(true);
        $query->update("#__".$item_type)->set("published=-1");

        if (is_array($item_id))
        {
            $query->where("id IN(" . implode(',',$item_id) . ")");
        }
        else
        {
            $query->where("id=" . $item_id);
        }

        $db->setQuery($query);

        if ($db->execute())
        {
            $data['success'] = true;
            $msg = TextHelper::_('COBALT_SUCCESSULLY_REMOVED_ITEM');
        }
        else
        {
            $data['success'] = false;
            $data['error_msg'] = $db->getErrorMsg();
            $msg = TextHelper::_('COBALT_ERROR_REMOVING_ITEM');
        }

        $redirect = $this->getInput()->get('page_redirect');

        if ($redirect)
        {
            $this->getApplication()->redirect(RouteHelper::_('index.php?view=' . $redirect), $msg);
        }
        else
        {
            $data['remove'] = $item_id;
            echo json_encode($data);
        }
    }
}
