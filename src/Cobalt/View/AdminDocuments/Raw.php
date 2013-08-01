<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\AdminDocuments;

use JUri;
use Joomla\View\AbstractHtmlView;
use Cobalt\Helper\UsersHelper;
use Cobalt\Model\Documents as DocumentsModel;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Raw extends AbstractHtmlView
{
    public function render($tpl = null)
    {
        //authenticate the current user to make sure they are an admin
        UsersHelper::authenticateAdmin();

        //Add styles for iframe popup
        echo "<link href='".JURI::base()."libraries/crm/media/css/style.css' type='text/css' rel='stylesheet' />";
        echo "<link href='".JURI::base()."libraries/crm/media/css/bootstrap.min.css' type='text/css' rel='stylesheet' />";

        //import document
        if ( is_array($_FILES) && count($_FILES) > 0 ) {
            $model = new DocumentsModel;
            $model->upload();
        }

        //display
        return parent::render();
    }

}
