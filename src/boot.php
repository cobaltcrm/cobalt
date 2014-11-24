<?php
/**
 * @package    Cobalt.CRM
 *
 * @copyright  Copyright (C) 2013 Webspark, LLC. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_CEXEC') or die;

@ini_set('magic_quotes_runtime', 0);

// composer libraries check
if (!file_exists(JPATH_VENDOR.'/autoload.php'))
{
    echo 'Run composer first. Read installation instructions.';
    exit();
}

// System includes.
require_once JPATH_LIBRARIES.'/import.php';
require_once JPATH_VENDOR.'/autoload.php';

// Alias the helper classes, so we don't have to add the use statement to every layout.
$helpers = glob(JPATH_ROOT . '/src/Cobalt/Helper/*.php');

foreach ($helpers as $classFile) {
    $className = basename(str_replace('.php', '', $classFile));
    class_alias('Cobalt\\Helper\\' . $className, $className);
}
