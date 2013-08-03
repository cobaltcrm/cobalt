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
<div class="container-fluid">
    <?php echo $this->menu['help_menu']->render(); ?>
    <div class="row">
        <div class="col-lg-12" id="content">
            <div id="system-message-container"></div>
            <div class="row">
                <h3><?php echo $this->header; ?></h3>
                <form action="index.php?view=documents" method="post" name="adminForm" id="adminForm" class="form-validate" >
                    <table>
                        <tr>
                            <td><b><?php echo JText::_('COBALT_NAME'); ?></b></td>
                            <td><input type="text" class="form-control" name="filename" value="<?php echo $this->document['filename']; ?>" /></td>
                        </tr>
                    </table>
                    <div>
                        <?php if ($this->document['id']) { ?>
                            <input type="hidden" name="id" value="<?php echo $this->document['id']; ?>" />
                        <?php } ?>
                        <input type="hidden" name="task" value="" />
                        <?php echo JHtml::_('form.token'); ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php $this->menu['quick_menu']->render(); ?>
</div>
