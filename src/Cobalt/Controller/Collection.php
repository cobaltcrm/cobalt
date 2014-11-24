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
use Cobalt\Model\Autocomplete as ModelAutocomplete;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Collection extends DefaultController
{
    public function execute()
    {
        $response = array();

        $fields = $this->getInput()->getString('fields');
        $object  = ucwords($this->getInput()->getCmd('object'));

        $model = new ModelAutocomplete();
        $model->setObject($object);

        if (empty($object))
        {
            $response['alert'] = array(
                'type' => 'error',
                'message' => 'Object request not found'
            );
            $this->getApplication()->close(json_encode($response));
        }

        try
        {
            $response = $model->getData(explode(',', $fields));
        }
        catch (\Exception $e)
        {
            $response['alert'] = array(
                'type' => 'error',
                'message' => $e->getMessage()
            );
        }

        $this->getApplication()->close(json_encode($response));
    }
}
