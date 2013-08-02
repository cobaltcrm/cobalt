<?php
defined('_CEXEC') or die;

$this
    // Add stylesheets
    ->addStyleSheet('/themes/bootstrap/css/bootstrap.min.css')

    // Add Scripts
    ->addScript('//code.jquery.com/jquery.js')
    ->addScript('/themes/bootstrap/js/bootstrap.min.js')

    // Add Meta tags
    ->addMetaTag('viewport', 'width=device-width, initial-scale=1.0')

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