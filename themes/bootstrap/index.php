<?php
/**
 * Cobalt CRM
 *
 * @copyright  Copyright (C) 2012 - 2014 cobaltcrm.org All Rights Reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

defined('_CEXEC') or die;

$view['assets']->addStylesheet('themes/bootstrap/css/bootstrap.css');
$view['assets']->addStylesheet('themes/bootstrap/css/cobalt.css');
$view['assets']->addStylesheet('themes/bootstrap/css/fullcalendar.css');

// Add core stylesheets
$view['assets']->addStylesheet('media/css/datepicker.css');
$view['assets']->addStylesheet('media/css/dataTables.foundation.css');

// Add theme Scripts
$view['assets']->addScript('themes/bootstrap/js/jquery.js');
$view['assets']->addScript('themes/bootstrap/js/jquery-ui.min.js');
$view['assets']->addScript('themes/bootstrap/js/bootstrap.min.js');
$view['assets']->addScript('themes/bootstrap/js/bootstrap-typeahead.min.js');
$view['assets']->addScript('themes/bootstrap/js/bloodhound.min.js');
$view['assets']->addScript('themes/bootstrap/js/jquery.cluetip.min.js');
$view['assets']->addScript('themes/bootstrap/js/fullcalendar.js');
$view['assets']->addScript('themes/bootstrap/js/ChartNew.js');
$view['assets']->addScript('themes/bootstrap/js/cobalt.js');

// Add core scripts
$view['assets']->addScript('media/js/bootstrap-datepicker.js');
$view['assets']->addScript('media/js/jquery.form.js');
$view['assets']->addScript('media/js/jquery.dataTables.min.js');
$view['assets']->addScript('media/js/dataTables.foundation.js');

/*if (strpos(\Cobalt\Factory::getApplication()->get('uri.route'), 'install') !== false) :
	$this->addStylesheet($this->baseurl . 'themes/bootstrap/jasny/css/jasny-bootstrap.min.css')
		->addStylesheet($this->baseurl . 'themes/bootstrap/css/install.css')
		->addScript($this->baseurl . 'themes/bootstrap/wizard/js/wizard.min.js')
		->addScript($this->baseurl . 'themes/bootstrap/js/install.js');
endif;*/
?>
<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Cobalt</title>
		<?php $view['assets']->outputHeadDeclarations(); ?>
	</head>
	<body>
		<?php $view['slots']->output('_content'); ?>
	</body>
</html>
