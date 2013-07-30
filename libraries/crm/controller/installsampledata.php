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

class CobaltControllerInstallSampleData extends CobaltControllerDefault
{

    public function installSampleData()
    {
        $app = JFactory::getApplication();
        $sampleIds = array();

        $importModel = new CobaltModelImport();
        $sampleCsvFiles = array(
                'sample-company'    => "companies",
                'sample-person'     => "people",
                'sample-deal'       => "deals"
                // 'sample-event'      => "events",
                // 'sample-goal'       => "goals",
            );
        foreach ($sampleCsvFiles as $file => $table) {
            $importData = $importModel->readCSVFile(JPATH_SITE.'/sample/'.$file.'.csv',$table,FALSE);
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
            $ids = $importModel->importCSVData($importData,$model,TRUE);
            $sampleIds[$table] = $ids;
        }

        $data = array('import_sample'=>serialize($sampleIds));

        $configModel = new CobaltModelConfig();
        $configModel->store($data);

        $msg = JText::_('COBALT_SAMPLE_DATA_INSTALLED');
        $app->redirect('index.php?view=import',$msg);

    }

}
