<?php
/**
 * @package    Joomla.Site
 *
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

if (version_compare(PHP_VERSION, '5.3.1', '<')) {
    die('Your host needs to use PHP 5.3.1 or higher to run this version of Joomla!');
}

/**
 * Constant that is checked in included files to prevent direct access.
 * define() is used in the installation folder rather than "const" to not error for PHP 5.2 and lower
 */
define('_JEXEC', 1);

if (file_exists(__DIR__ . '/defines.php')) {
    include_once __DIR__ . '/defines.php';
}

if (!defined('_JDEFINES')) {
    define('JPATH_BASE', __DIR__);
    require_once JPATH_BASE . '/includes/defines.php';

}

require_once JPATH_BASE . '/includes/framework.php';
include_once JPATH_BASE . '/includes/application.php';

try {
    $app = JApplicationWeb::getInstance('cobalt');
    JFactory::$application = $app;

    // Initialise the application.
    $app->initialise();

    // Route the application.
    $app->route();

    // Dispatch the application.
    $app->execute();

} catch (Exception $e) {
    print_r($e);

    return;
}
