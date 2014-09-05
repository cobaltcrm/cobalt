<?php
/**
 * @package    Cobalt.CRM
 *
 * @copyright  Copyright (C) 2012 Cobalt. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_CEXEC') or die;

/*
 * Define Cobalt's version number here, update this at release time
 *
 * Always ensure -dev is appended while in development between releases
 * and that the base version number is the next planned release.
 *
 * i.e.  If 1.0.0 was released, this should be 1.0.1-dev
 */
define('COBALT_VERSION', '1.0.0-dev');

// Cobalt Application defines.
define('JPATH_ROOT',          dirname(__DIR__));
define('JPATH_BASE',          JPATH_ROOT);
define('JPATH_SITE',          JPATH_ROOT);
define('JPATH_COBALT',		  JPATH_ROOT . '/src/Cobalt');
define('JPATH_CONFIGURATION', JPATH_ROOT);
define('JPATH_ADMINISTRATOR', JPATH_ROOT . '/admin');
define('JPATH_LIBRARIES',     JPATH_ROOT . '/libraries');
define('JPATH_PLUGINS',       JPATH_ROOT . '/plugins');
define('JPATH_INSTALLATION',  JPATH_ROOT . '/install');
define('JPATH_THEMES',        JPATH_BASE . '/themes');
define('JPATH_CACHE',         JPATH_BASE . '/cache');
define('JPATH_VENDOR',        JPATH_BASE . '/vendor');
define('JROUTER_MODE_SEF', 	  1);
