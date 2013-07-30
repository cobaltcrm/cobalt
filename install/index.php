<?php

//joomla includes
define('_JEXEC', 1);
define('JPATH_BASE', dirname(dirname(__FILE__)));
include_once JPATH_BASE."/includes/defines.php";
include_once JPATH_BASE."/install/helpers/uri.php";
require_once JPATH_LIBRARIES.'/import.php';

//handle ajax requests
if (array_key_exists('c',$_REQUEST)) {

    require_once getcwd().'/controller/'.$_REQUEST['c'].".php";
    $name = "crm".ucwords($_REQUEST['c'])."Controller";
    $c = new $name();
    $c->$_REQUEST['m']();

} else {

    //require the crm installer script
    require_once getcwd().'/install.php';

    $app = new crmInstall();
    $app->install();

}
