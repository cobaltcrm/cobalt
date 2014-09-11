<?php
defined('_CEXEC') or die;
unset($this->_scripts);
unset($this->_styleSheets);
$this->_style = array();

$this
    // Add theme stylesheets
    ->addStyleSheet($this->baseurl.'/themes/bootstrap/css/bootstrap.css')
    ->addStyleSheet($this->baseurl.'/themes/bootstrap/css/cobalt.css')

    // Add core stylesheets
    ->addStyleSheet($this->baseurl.'/src/Cobalt/media/css/datepicker.css')
    ->addStyleSheet($this->baseurl.'/src/Cobalt/media/css/dataTables.bootstrap.css')

    // Add theme Scripts
    ->addScript($this->baseurl.'/themes/bootstrap/js/jquery.js')
    ->addScript($this->baseurl.'/themes/bootstrap/js/bootstrap.min.js')
    ->addScript($this->baseurl.'/themes/bootstrap/js/cobalt.js')

    // Add core scripts
    ->addScript($this->baseurl.'/src/Cobalt/media/js/bootstrap-datepicker.js')
    ->addScript($this->baseurl.'/src/Cobalt/media/js/jquery.form.js')
    ->addScript($this->baseurl.'/src/Cobalt/media/js/jquery.dataTables.min.js')
    ->addScript($this->baseurl.'/src/Cobalt/media/js/dataTables.bootstrap.js')


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