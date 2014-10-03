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

/*
 * Constant that is checked in included files to prevent direct access.
 * define() is used here rather than "const" to not error for PHP 5.2 and lower
 */
define('_CEXEC', 1);

/*
 * Define Cobalt's version number here, update this at release time
 *
 * Always ensure -dev is appended while in development between releases
 * and that the base version number is the next planned release.
 *
 * i.e.  If 1.0.0 was released, this should be 1.0.1-dev
 */
define('COBALT_VERSION', '1.0.0-dev');

/*
 * Define whether the application was launched from the command line or a web request
 */
$isCli = defined('STDIN') && defined('STDOUT') && isset($_SERVER['argv']) && php_sapi_name() === 'cli';
define('COBALT_CLI', $isCli);

/*
 * Users are able to move files in the filesystem, for example to move all non-web assets outside the webroot.
 * To accomplish this, a user must copy the <JPATH_ROOT>/src/defines.php file to the same folder as this index.php file
 * AND set the '_CDEFINES' define to true, otherwise the system defaults will be used
 */

if (file_exists(__DIR__ . '/defines.php'))
{
	require_once __DIR__ . '/defines.php';
}

/*
 * We also allow for instances where the index.php file may be symlinked outside the web but other necessary files
 * such as the configuration.php file are stored in it.  In this instance, we need to check the $_SERVER var for our
 * correct document root path as the above checks the symlinked location
 */
if (!defined('_CDEFINES'))
{
	if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/defines.php'))
	{
		require_once $_SERVER['DOCUMENT_ROOT'] . '/defines.php';
	}
}

/*
 * If at this point _CDEFINES hasn't been defined, we will assume that a custom defines file has not been included and we
 * will use the system default path.
 */
if (!defined('_CDEFINES'))
{
	define('_CDEFINES', false);
	require_once __DIR__ . '/src/defines.php';
}

require_once __DIR__ . '/src/boot.php';

if (COBALT_CLI)
{
	try
	{
		$app = new \Cobalt\CLI\Application;
		$app->execute();
	}
	catch (\Exception $e)
	{
		fwrite(STDOUT, "\nERROR: " . $e->getMessage() . "\n");
		fwrite(STDOUT, "\n" . $e->getTraceAsString() . "\n");

		exit;
	}
}
else
{
	\Tracy\Debugger::enable();

	$app = new \Cobalt\Application;
	$app->execute();
}
