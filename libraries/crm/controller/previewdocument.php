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

class CobaltControllerPreviewDocument extends CobaltControllerDefault
{

     function execute()
     {
        $model = new CobaltModelDocument();
        $document = $model->getDocument();

        $filename = basename($document->name);
        $file_extension = strtolower(substr(strrchr($filename,"."),1));

        switch ($file_extension) {
            case "gif": $ctype="image/gif"; break;
            case "png": $ctype="image/png"; break;
            case "jpeg":
            case "jpg": $ctype="image/jpg"; break;
            default:
        }

        header('Content-type: '.$ctype);
        ob_clean();
        flush();
        readfile($document->path);
        exit;
   }

}
