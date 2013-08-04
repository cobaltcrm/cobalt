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

const _CEXEC = 1;

require_once __DIR__ . '/src/boot.php';

// $container is setup in the previous require.
$container->resolve('app')->execute();
