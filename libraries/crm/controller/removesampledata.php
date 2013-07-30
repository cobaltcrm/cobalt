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

class CobaltControllerRemoveSampleData extends CobaltControllerDefault
{

    public function removeSampleData()
    {
        $app = JFactory::getApplication();

        $sampleIds = unserialize(CobaltHelperConfig::getConfigValue('import_sample'));

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        foreach ($sampleIds as $table => $ids) {
                $query->clear()
                    ->delete("#__".$table)
                    ->where("id IN(".implode(',',$ids).")");
                $db->setQuery($query);
                $db->query();
        }

        $data = array('import_sample'=>"0");

        $configModel = new CobaltModelConfig();
        $configModel->store($data);

        $msg = JText::_('COBALT_SAMPLE_DATA_REMOVED');
        $app->redirect('index.php?view=import',$msg);

    }

}
