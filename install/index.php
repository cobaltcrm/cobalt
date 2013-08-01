<?php

//joomla includes
const _CEXEC = 1;

include dirname(__DIR__) . '/src/boot.php';

//handle ajax requests
if (array_key_exists('c', $_REQUEST)) {
    require_once __DIR__ . '/controller/'.$_REQUEST['c'].".php";
    $name = "crm".ucwords($_REQUEST['c'])."Controller";
    $c = new $name();
    $c->$_REQUEST['m']();
} else {
    //require the crm installer script
    require_once __DIR__.'/install.php';

    $app = new crmInstall();
    $app->install();

}
