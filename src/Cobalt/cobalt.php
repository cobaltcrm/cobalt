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

/** @type \Cobalt\Application $this */

use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\ActivityHelper;
use Cobalt\Helper\DateHelper;
use Cobalt\Helper\TemplateHelper;
use Cobalt\Helper\StylesHelper;

// Load Language
UsersHelper::loadLanguage();

//set site timezone
$tz = DateHelper::getSiteTimezone();

//get user object
$user = $this->getUser();

// Fetch the controller
$controllerObj = $this->getRouter()->getController($this->get('uri.route'));

// Require specific controller if requested
$controller = $this->input->get('controller', 'default');

//load user toolbar
$format = $this->input->get('format');

$overrides = array('ajax', 'mail', 'login');

$loggedIn = $user->isAuthenticated();

if ($loggedIn && $format !== 'raw' && !in_array($controller, $overrides)) {

    ActivityHelper::saveUserLoginHistory();

    // Set a default view if none exists
    if (! $this->input->get('view')) {
        $this->input->set('view', 'dashboard' );
    }

    //Grab document instance
    $document = $this->getDocument();

    //start component div wrapper
    if ( $this->input->get('view') != "print") {
        TemplateHelper::loadToolbar();
    }
    TemplateHelper::startCompWrap();

    //load javascript language
    TemplateHelper::loadJavascriptLanguage();

    TemplateHelper::showMessages();
}

if (!$loggedIn && !($controllerObj instanceof Cobalt\Controller\Login)) {
    $this->redirect(RouteHelper::_('index.php?view=login'));
}

//fullscreen detection
if (UsersHelper::isFullscreen()) {
    $this->input->set('tmpl', 'component' );
}

// Perform the Request task
$controllerObj->execute();

//end componenet wrapper
if ($user !== false && $format !== 'raw') {
    TemplateHelper::endCompWrap();
}
