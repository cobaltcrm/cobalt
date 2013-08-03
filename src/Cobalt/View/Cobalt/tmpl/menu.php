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
<div class="col-lg-3">
    <div class="sidebar-nav">
        <ul class="nav nav-list">
            <li class="nav-header"><?php echo JText::_('Cobalt Menu'); ?></li>
            <?php $app = JFactory::getApplication(); ?>
            <?php $view = $app->input->get('view'); ?>
            <?php if ( count($this->menu_links) > 0 ){ foreach ($this->menu_links as $menu_link) { ?>
               <?php $active = $view == $menu_link['view'] ? "active" : ""; ?>
                <li class="<?php echo $active; ?>" >
                    <i class="<?php echo $menu_link['class']; ?>"></i>
                    <span id="<?php echo $menu_link['id']; ?>">
                        <a href="<?php echo $menu_link['link']; ?>" rel="tooltip" data-placement="right" title="<?php echo $menu_link['tooltip']; ?>">
                            <?php echo $menu_link['text']; ?>
                        </a>
                    </span>
                </li>
            <?php } } ?>
        </ul>
    </div>
</div>
