<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/
// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' ); ?>
<script type="text/javascript">
       setTimeout(function(){window.top.location = '<?php echo LinkHelper::viewAdminDocuments(); ?>';},1000);
</script>
<?php echo TextHelper::_('COBALT_UPLOAD_SUCCESS');
