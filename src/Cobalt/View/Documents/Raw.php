<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\Documents;

use Joomla\View\AbstractHtmlView;
use Cobalt\Factory;
use Cobalt\Model\Document as DocumentModel;

defined( '_CEXEC' ) or die( 'Restricted access' );

class Raw extends AbstractHtmlView
{
    public function render($tpl = null)
    {
        $app = Factory::getApplication();

         //get model
        $model = new DocumentModel;
        $documents = $model->getDocuments($app->input->get('document_id'));
        $state = $model->getState();
        $layout = $this->getLayout();

        if ( $app->input->get('document_id') && $layout != "document_row" ) {
            $documents = $documents[0];
        }

        //assign refs
        $this->documents = $documents;
        $this->state = $state;

        //display view
        echo parent::render();
    }

}
