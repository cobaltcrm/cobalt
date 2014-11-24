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

use Cobalt\Model\Document as DocumentModel;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class PreviewDocument extends DefaultController
{
    public function execute()
    {
        $model = new DocumentModel;
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
