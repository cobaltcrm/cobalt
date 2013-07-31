<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/
// No direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\DateHelper;
use Cobalt\Helper\TemplateHelper;
use Cobalt\Helper\StylesHelper;

CobaltHelperCobalt::loadHelpers();

// Load Language
UsersHelper::loadLanguage();

//set site timezone
$tz = DateHelper::getSiteTimezone();

//load tables
JTable::addIncludePath(JPATH_COBALT.'/tables');

//Load plugins
JPluginHelper::importPlugin('cobalt');

//application
$app = JFactory::getApplication();

// Require specific controller if requested
if ($controller = $app->input->get('controller','default')) {
    require_once (JPATH_COBALT.'/controller/'.$controller.'.php');
}

//load user toolbar
$format = $app->input->get('format');
$overrides = array('ajax','mail','login');

if ( $format != "raw" && !in_array($controller,$overrides) ) {

    // Set a default view if none exists
    if ( ! JFactory::getApplication()->input->get( 'view' ) ) {
            JFactory::getApplication()->input->set('view', 'dashboard' );
    }

    //Grab document instance
    $document = JFactory::getDocument();

    //load scripts
    $document->addScript( JURI::base().'libraries/crm/media/js/jquery.js' );
    $document->addScript( JURI::base().'libraries/crm/media/js/jquery-ui.js' );
    $document->addScript( JURI::base().'libraries/crm/media/js/jquery.tools.min.js' );
    $document->addScript( JURI::base().'libraries/crm/media/js/bootstrap.min.js' );
    $document->addScript( JURI::base().'libraries/crm/media/js/bootstrap-colorpicker.js' );
    $document->addScript( JURI::base().'libraries/crm/media/js/bootstrap-datepicker.js' );
    $document->addScript( JURI::base().'libraries/crm/media/js/bootstrap-fileupload.js' );

    //start component div wrapper
    if ( $app->input->get('view') != "print") {
        TemplateHelper::loadToolbar();
    }
    TemplateHelper::startCompWrap();

        //mobile detection
        if (TemplateHelper::isMobile()) {
               $app->input->set('tmpl','component');
             $document->addScript('http://maps.google.com/maps/api/js?sensor=false');
             $document->addScript( JURI::base().'libraries/crm/media/js/jquery.mobile.1.0.1.min.js' );
             $document->addScript( JURI::base().'libraries/crm/media/js/jquery.mobile.datepicker.js' );
             $document->addScript( JURI::base().'libraries/crm/media/js/jquery.mobile.map.js' );
             $document->addScript( JURI::base().'libraries/crm/media/js/jquery.mobile.map.extensions.js' );
             $document->addScript( JURI::base().'libraries/crm/media/js/jquery.mobile.map.services.js' );
             $document->addScript( JURI::base().'libraries/crm/media/js/cobalt.mobile.js');
             $document->setMetaData('viewport','width=device-width, initial-scale=1');
        } else {
            //load task events javascript which will be used throughout page redirects
            $document->addScript( JURI::base().'libraries/crm/media/js/timepicker.js');
            $document->addScript( JURI::base().'libraries/crm/media/js/cobalt.js' );
            $document->addScript( JURI::base().'libraries/crm/media/js/filters.js');
            $document->addScript( JURI::base().'libraries/crm/media/js/autogrow.js');
            $document->addScript( JURI::base().'libraries/crm/media/js/jquery.cluetip.min.js');

        }

    //load styles
    StylesHelper::loadStyleSheets();

    //get user object
    $user = UsersHelper::getLoggedInUser();

    //if the user is logged in continue else redirect to joomla login
    if ($user) {
        CobaltHelperActivity::saveUserLoginHistory();
    } elseif ($app->input->getWord('view')!='login' && $app->input->getWord('controller')!='login') {
        $app->redirect('index.php?view=login');
    }

}

//load javascript language
TemplateHelper::loadJavascriptLanguage();

// Create the controller
$classname	= 'CobaltController'.$controller;
$app = JFactory::getApplication();
$controller = new $classname($app->input, $app);

//fullscreen detection
if (UsersHelper::isFullscreen()) {
    JFactory::getApplication()->input->set('tmpl', 'component' );
}

// Perform the Request task
$controller->execute();

//end componenet wrapper
if ($format != "raw" && $controller != "ajax") {
    TemplateHelper::endCompWrap();
}
