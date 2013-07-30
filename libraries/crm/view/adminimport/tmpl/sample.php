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
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<div class="container-fluid">
    <?php echo $this->menu['help_menu']->render(); ?>
    <?php echo $this->menu['quick_menu']->render(); ?>
    <div class="row-fluid">
        <div class="span12" id="content">
            <div id="system-message-container"></div>
            <div class="row-fluid">
                <?php echo $this->menu['menu']->render(); ?>
                <div class="span9">
                    <div class="row-fluid">
                        <div class="sample_text">
                            <h1><?php echo JText::_('COBALT_INSTALL_SAMPLE_DATA_TITLE'); ?></h1>
                            <p><div class="alert alert-info"><?php echo JText::_('COBALT_INSTALL_SAMPLE_DATA_DESC'); ?></div></p>
                            <form action="index.php?view=import" method="post" name="adminForm" id="adminForm" class="inline-form"  >
                                <input type="submit" value="<?php echo JText::_('COBALT_INSTALL_SAMPLE_BUTTON'); ?>" class="btn btn-primary btn-large" />
                                <input type="hidden" name="id" value="1" />
                                <input type="hidden" name="task" value="installSampleData" />
                                <input type="hidden" name="controller" value="import" />
                                <input type="hidden" name="view" value="import" />
                                <input type="hidden" name="layout" value="sample" />
                                <?php echo JHtml::_('form.token'); ?>
                            </form>
                            <form action="index.php?view=import" method="post" name="adminForm" id="adminForm" class="inline-form"  >
                                <input type="submit" value="<?php echo JText::_('COBALT_REMOVE_SAMPLE_BUTTON'); ?>" class="btn btn-danger btn-large" />
                                <input type="hidden" name="id" value="1" />
                                <input type="hidden" name="task" value="removeSampleData" />
                                <input type="hidden" name="controller" value="import" />
                                <input type="hidden" name="view" value="import" />
                                <input type="hidden" name="layout" value="sample" />
                                <?php echo JHtml::_('form.token'); ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
