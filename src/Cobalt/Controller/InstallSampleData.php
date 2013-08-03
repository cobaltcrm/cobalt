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
use Cobalt\Model\Import as ImportModel;
use Cobalt\Model\Config as ConfigModel;
use Cobalt\Helper\TextHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class InstallSampleData extends DefaultController
{
    public function execute()
    {
        $app = JFactory::getApplication();
        $sampleIds = array();

        $importModel = new ImportModel;
        $sampleCsvFiles = array(
                'sample-company'    => "companies",
                'sample-person'     => "people",
                'sample-deal'       => "deals"
                // 'sample-event'      => "events",
                // 'sample-goal'       => "goals",
            );
        foreach ($sampleCsvFiles as $file => $table) {
            $importData = $importModel->readCSVFile(JPATH_COBALT.'/sample/'.$file.'.csv', $table, false);
            switch ($table) {
                case "companies":
                    $model = "company";
                break;
                case "people":
                    $model = "people";
                break;
                case "deals":
                    $model = "deal";
                break;
            }
            unset($importData['headers']);
            $ids = $importModel->importCSVData($importData, $model, true);
            $sampleIds[$table] = $ids;
        }

        $data = array('import_sample' => serialize($sampleIds));

        $configModel = new ConfigModel;
        $configModel->store($data);

        $msg = TextHelper::_('COBALT_SAMPLE_DATA_INSTALLED');
        $app->redirect('index.php?view=import', $msg);
    }

}
