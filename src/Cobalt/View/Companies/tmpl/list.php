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

<thead>
    <th class="checkbox_column"><input type="checkbox" onclick="Cobalt.selectAll(this);" /></th>
    <th class="name"><?php echo TextHelper::_('COBALT_COMPANIES_NAME'); ?></th>
    <th class="contact"><?php echo ucwords(TextHelper::_('COBALT_CONTACT_DETAILS')); ?></th>
    <th class="created" ><?php echo TextHelper::_('COBALT_COMPANIES_ADDED'); ?></th>
    <th class="updated" ><?php echo TextHelper::_('COBALT_COMPANIES_UPDATED'); ?></th>
    <th class="notes" >&nbsp;</th>
</thead>
