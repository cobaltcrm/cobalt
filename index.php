<?php
/**
 * @package    Cobalt.CRM
 *
 * @copyright  Copyright (C) 2013 Webspark, LLC. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

if (version_compare(PHP_VERSION, '5.3.10', '<')) {
	die('Your host needs to use PHP 5.3.10 or higher to run this version of Cobalt!');
}

define('_CEXEC', 1);

require_once __DIR__ . '/src/boot.php';

use Tracy\Debugger;
Debugger::enable();

// $container is setup in the previous require.
$app = new \Cobalt\Application;
$app->execute();
