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

class CobaltViewGlobalHtml extends JViewHTML
{

    public function render()
    {
        //app
        $app = JFactory::getApplication();
        //document
        $document = JFactory::getDocument();

        //javascripts
        if ($this->getLayout()=='header') {
            $document->addScriptDeclaration('var base_url = "<?php echo JURI::base(); ?>";');
        }

        //mobile detection
        $this->isMobile = CobaltHelperTemplate::isMobile();
        $this->isDashboard = $app->input->get('view')=='dashboard' ? true : false;

        return parent::render();
    }
}
