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

use Cobalt\Helper\TextHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class DownloadImportTemplate extends DefaultController
{
    public function execute()
    {
        $template_type = $this->getInput()->get('template_type');

        $path = JPATH_SITE . '/media/import_templates/import_' . $template_type . '.csv';

        if (!file_exists($path))
        {
            $msg = TextHelper::_('COBALT_EXPORT_MISSING_FILE') . ' (' . $path . ')';
            $this->getApplication()->redirect('index.php?view=import&import_type=' . $template_type, $msg, 'danger');
        }
        else
        {
            ob_start();
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="import_'.$template_type.'.csv"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($path));
            ob_clean();
            flush();

            readfile($path);

            exit();
        }
    }
}
