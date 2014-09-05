<?php

//joomla includes
const _CEXEC = 1;

if (!defined('_JDEFINES')) {
    require_once dirname(dirname(__FILE__)) . '/src/defines.php';
}

require_once JPATH_VENDOR.'/autoload.php';
require_once JPATH_LIBRARIES . '/import.php';

JLoader::registerPrefix('crm', __DIR__);

JApplicationWeb::getInstance('crmApplication')->execute();