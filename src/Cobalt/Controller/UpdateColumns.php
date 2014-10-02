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

use Cobalt\Model\User as UserModel;
// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class UpdateColumns extends DefaultController
{
   public function execute()
   {
       //get the location of the page
       $loc = $this->getInput()->get('loc');

       //get new data to insert into user tables
       $column = $this->getInput()->get('column');

       //get model
       $model = new UserModel;
       $model->updateColumns($loc,$column);

   }

}
