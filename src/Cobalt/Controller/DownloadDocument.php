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

class DownloadDocument extends DefaultController
{
    public function execute()
    {
        $model = new DocumentModel;
        $document = $model->getDocument();

        $filename = basename($document->name);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.$filename);
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($document->path));
        readfile($document->path);
        exit;
    }
}
