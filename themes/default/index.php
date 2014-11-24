<?php
defined('_CEXEC') or die;

use Cobalt\Helper\UsersHelper;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <jdoc:include type="head" />
    <link rel="stylesheet" href="<?php echo $this->baseurl ?>/themes/default/css/default.css" />
</head>
<body class="contentpane">
    <jdoc:include type="message" />
    <?php if ( UsersHelper::isAdmin() && count(JToolbar::getInstance()->getItems()) > 0 ) {
        echo '<div class="container">'.JToolbar::getInstance()->render().'</div>';
    } ?>
    <jdoc:include type="crm" />
</body>
</html>
