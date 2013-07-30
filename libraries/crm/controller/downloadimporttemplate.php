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

class CobaltControllerDownloadImportTemplate extends CobaltControllerDefault
{
       function execute()
       {
            $app = JFactory::getApplication();

            $template_type = $app->input->get('template_type');

            $path = JPATH_SITE.'/media/import_templates/import_'.$template_type.'.csv';

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
