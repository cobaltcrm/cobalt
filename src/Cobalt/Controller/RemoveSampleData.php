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
use Cobalt\Helper\ConfigHelper;
use Cobalt\Helper\TextHelper;
use Cobalt\Model\Config as ConfigModel;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class RemoveSampleData extends DefaultController
{

    public function removeSampleData()
    {
        $app = JFactory::getApplication();

        $sampleIds = unserialize(ConfigHelper::getConfigValue('import_sample'));

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        foreach ($sampleIds as $table => $ids) {
                $query->clear()
                    ->delete("#__".$table)
                    ->where("id IN(".implode(',',$ids).")");
                $db->setQuery($query);
                $db->query();
        }

        $data = array('import_sample'=>"0");

        $configModel = new ConfigModel;
        $configModel->store($data);

        $msg = TextHelper::_('COBALT_SAMPLE_DATA_REMOVED');
        $app->redirect('index.php?view=import',$msg);

    }

}