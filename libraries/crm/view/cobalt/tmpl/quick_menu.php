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
<?php /**
<div class="quick-icons">
        <?php if ( count( $this->quick_menu_links) > 0 ) { foreach ($this->quick_menu_links as $quick_menu_link) { ?>
                <a class="btn btn-mini" href="<?php echo $quick_menu_link['link']; ?>">
                    <i class="<?php echo $quick_menu_link['class']; ?>"></i>
                    <span>
                        <?php echo $quick_menu_link['text']; ?>
                    </span>
                </a>
        <?php } } ?>
</div>
**/
