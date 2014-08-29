<?php
defined('_CEXEC') or die;
unset($this->_scripts);
unset($this->_styleSheets);
$this->_style = array();

$this
    // Add stylesheets
    ->addStyleSheet($this->baseurl.'/themes/bootstrap/css/bootstrap.css')
    ->addStyleSheet($this->baseurl.'/themes/bootstrap/css/bootstrap-responsive.css')
    ->addStyleSheet($this->baseurl.'/themes/bootstrap/css/cobalt.css')

    // Add theme Scripts
    ->addScript($this->baseurl.'/themes/bootstrap/js/jquery.js')
    ->addScript($this->baseurl.'/themes/bootstrap/js/jquery-migrate-1.2.1.min.js')
    ->addScript($this->baseurl.'/themes/bootstrap/js/bootstrap.min.js')
    ->addScript($this->baseurl.'/themes/bootstrap/js/cobalt.js')

    // Add core scripts
    ->addScript($this->baseurl.'/src/Cobalt/media/js/bootstrap-datepicker.js')
    // ->addScript($this->baseurl.'/src/Cobalt/media/js/cobalt.js')

    // Add Meta tags
    ->setMetaData('viewport', 'width=device-width, initial-scale=1.0')

    // Other Options
    ->setTab("\t")
    ->setBase(null)
    ->setGenerator('Cobalt CRM');
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