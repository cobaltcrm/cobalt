<?php

//joomla includes
const _CEXEC = 1;

if (!defined('_JDEFINES')) {
    require_once dirname(dirname(__FILE__)) . '/src/defines.php';
}

require_once JPATH_VENDOR.'/autoload.php';

require_once JPATH_LIBRARIES . DIRECTORY_SEPARATOR . 'import.php';
JLoader::register('crmInstallModel', __DIR__ . '/model/install.php');

//handle ajax requests
if (array_key_exists('c', $_REQUEST))
{
    require_once __DIR__ . '/controller/'.$_REQUEST['c'].".php";
    $name = "crm".ucwords($_REQUEST['c'])."Controller";
    $c = new $name();
    $c->$_REQUEST['m']();
}
else
{
    //require the crm installer script
    require_once __DIR__.'/install.php';

    $app = new crmInstall();
    $app->install();
}
