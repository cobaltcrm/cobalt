<?php
defined('_CEXEC') or die;
unset($this->_scripts);
unset($this->_styleSheets);
$this->_style = array();
$this->baseurl = \Cobalt\Container::fetch('app')->get('uri.base.full');
$this->mediaurl = \Cobalt\Container::fetch('app')->get('uri.media.full');

$this
    // Add theme stylesheets
    ->addStyleSheet($this->baseurl.'themes/bootstrap/css/bootstrap.css')
    ->addStyleSheet($this->baseurl.'themes/bootstrap/css/cobalt.css')
    ->addStyleSheet($this->baseurl.'themes/bootstrap/css/fullcalendar.css')

    // Add core stylesheets
    ->addStyleSheet($this->mediaurl.'css/datepicker.css')
    ->addStyleSheet($this->mediaurl.'css/dataTables.foundation.css')

    // Add theme Scripts
    ->addScript($this->baseurl.'themes/bootstrap/js/jquery.js')
    ->addScript($this->baseurl.'themes/bootstrap/js/jquery-ui.min.js')
    ->addScript($this->baseurl.'themes/bootstrap/js/bootstrap.min.js')
    ->addScript($this->baseurl.'themes/bootstrap/js/bootstrap-typeahead.min.js')
    ->addScript($this->baseurl.'themes/bootstrap/js/bloodhound.min.js')
    ->addScript($this->baseurl.'themes/bootstrap/js/jquery.cluetip.min.js')
    ->addScript($this->baseurl.'themes/bootstrap/js/fullcalendar.js')
    ->addScript($this->baseurl.'themes/bootstrap/js/ChartNew.js')
    ->addScript($this->baseurl.'themes/bootstrap/js/cobalt.js')

    // Add core scripts
    ->addScript($this->mediaurl.'js/bootstrap-datepicker.js')
    ->addScript($this->mediaurl.'js/jquery.form.js')
    ->addScript($this->mediaurl.'js/jquery.dataTables.min.js')
    ->addScript($this->mediaurl.'js/dataTables.foundation.js')


    // Add Meta tags
    ->setMetaData('viewport', 'width=device-width, initial-scale=1.0')

    // Other Options
    ->setTab("\t")
    ->setBase(null)
    ->setGenerator('Cobalt CRM');

if (strpos(\Cobalt\Container::fetch('app')->get('uri.route'), 'install') !== false) :
	$this->addStylesheet($this->baseurl . 'themes/bootstrap/jasny/css/jasny-bootstrap.min.css')
		->addStylesheet($this->baseurl . 'themes/bootstrap/css/install.css')
		->addScript($this->baseurl . 'themes/bootstrap/wizard/js/wizard.min.js')
		->addScript($this->baseurl . 'themes/bootstrap/js/install.js');
endif;
?>
<!DOCTYPE html>
<html>
<head>
    <jdoc:include type="head" />
</head>
<body>
    <jdoc:include type="message" />
    <jdoc:include type="cobalt" />
</body>
</html>
