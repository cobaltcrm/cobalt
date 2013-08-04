<?php
/**
 * @package    Cobalt.CRM
 *
 * @copyright  Copyright (C) 2013 Webspark, LLC. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_CEXEC') or die;

if (!defined('_JDEFINES')) {
    require_once __DIR__ . '/defines.php';
}

@ini_set('magic_quotes_runtime', 0);

//
// Installation check, and check on removal of the install directory.
//
//if (!file_exists(JPATH_CONFIGURATION.'/configuration.php') || (filesize(JPATH_CONFIGURATION.'/configuration.php') < 10) || file_exists(JPATH_INSTALLATION.'/index.php')) {
//
//    if (file_exists(JPATH_INSTALLATION.'/index.php')) {
//
//        header('Location: '.substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], 'install/index.php')).'install/index.php');
//        exit();
//
//    } else {
//        echo 'No configuration file found and no installation code available. Exiting...';
//        exit();
//    }
//}

// System includes.
require_once JPATH_LIBRARIES.'/import.php';
require_once JPATH_VENDOR.'/autoload.php';
require_once JPATH_CONFIGURATION.'/configuration.php';

JLoader::register('JRoute', JPATH_ROOT . '/src/compat/JRoute.php');
JLoader::registerPrefix('Modular', JPATH_SITE.'/libraries/modular/');

$container = Cobalt\Container::getInstance();

$container
    ->registerServiceProvider(new Cobalt\Provider\ConfigServiceProvider)
    ->registerServiceProvider(new Cobalt\Provider\DatabaseServiceProvider)
    ->registerServiceProvider(new Cobalt\Provider\WhoopsServiceProvider);

$container->bind('app', function($c) {
        static $app;

        if (is_null($app)) {
            /** @var $c \Cobalt\Container */
            $c->registerProviders();

            $app = new Cobalt\Application;
        }

        return $app;
    });

// Alias the helper classes, so we don't have to add the use statement to every layout.
$helpers = glob(JPATH_ROOT . '/src/Cobalt/Helper/*.php');

foreach ($helpers as $classFile) {
    $className = basename(str_replace('.php', '', $classFile));
    class_alias('Cobalt\\Helper\\' . $className, $className);
}
