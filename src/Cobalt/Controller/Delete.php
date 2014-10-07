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

class Delete extends DefaultController
{
    public function execute()
    {
        $view = $this->getInput()->get("view");
        $modelName = "Cobalt\\Model\\".ucwords($this->getInput()->get('item_type'));
        $model = new $modelName();
        $response = new \stdClass;

        try
        {
        	$model->delete($this->getInput()->get('item_id', null, 'array'));
        	$response->alert = new \stdClass;
            $response->alert->message = TextHelper::_('COBALT_DELETED');
            $response->alert->type = 'success';
        }
        catch(\UnexpectedValueException $e)
        {
        	$response->alert = new \stdClass;
            $response->alert->message = $e->getMessage();
            $response->alert->type = 'danger';
        }

        if ($this->isAjaxRequest())
        {
        	$this->getApplication()->close(json_encode($response));
        }
        else
        {
            $this->getApplication()->redirect("index.php?view=" . $view);
        }
    }
}
