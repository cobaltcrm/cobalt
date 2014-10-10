<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\GlobalMarkup;

use Joomla\View\AbstractHtmlView;
use Cobalt\Factory;
use Cobalt\Helper\TemplateHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Html extends AbstractHtmlView
{

    public function render()
    {
        //app
        $app = Factory::getApplication();
        //document
        $document = $app->getDocument();

        //javascripts
        if ($this->getLayout()=='header') {
            $document->addScriptDeclaration('var base_url = "'.$app->get('uri.base.full').'";');
        }

        //mobile detection
        $this->isMobile = TemplateHelper::isMobile();
        $this->isDashboard = $app->input->get('view')=='dashboard' ? true : false;

        return parent::render();
    }
}
