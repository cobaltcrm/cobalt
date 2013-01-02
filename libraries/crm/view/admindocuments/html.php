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

class CobaltViewAdmindocumentsHtml extends JViewHtml{

    /**
     * display method
     * @return void
     **/
    function render($tpl = null)
    {

        //authenticate the current user to make sure they are an admin
        CobaltHelperUsers::authenticateAdmin();
        
        //get the layout
        $layout = $this->getLayout();

        //gather information for view
        $model = new CobaltModelDocuments();
        $model->set("_layout",$layout);
        
        //add javascript
        $document =& JFactory::getDocument();
        $document->addScript(JURI::base().'libraries/crm/media/js/cobalt-admin.js');

        if ( $layout != "upload" ){
            /** Menu Links **/
            $menu = CobaltHelperMenu::getMenuModules();
            $this->menu = $menu;
        }
        
        //determine layout type
        if ( $layout && $layout == 'edit' ){
            
            CRMToolbarHelper::cancel('cancel');
            CRMToolbarHelper::save('save');
            
        }else{
            
            //buttons
            CRMToolbarHelper::popup( 'upload', CRMText::_('COBALT_UPLOAD'), 'index.php?view=admindocuments&layout=upload&format=raw', 375, 150 );
            CRMToolbarHelper::deleteList(JText::_('COBALT_CONFIRMATION'),'remove');
            
            $documents = $model->getDocuments();
            $this->documents = $documents;
                
            // Initialise state variables.
            $state = $model->getState();
            $this->state = $state;

            $this->listOrder = $state->get('Documents.filter_order');
            $this->listDirn   = $state->get('Documents.filter_order_Dir');
        }
        
        //display
        return parent::render();
    }
}