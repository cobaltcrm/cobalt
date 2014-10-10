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
    <tr>
        <th class="checkbox_column"><input type="checkbox" onclick="Cobalt.selectAll(this);" title="<?php echo TextHelper::_('COBALT_CHECK_ALL_ITEMS'); ?>" data-placement="bottom" type="checkbox" /></th>
        <th class="avatar" ></th>
        <th><?php echo TextHelper::_('COBALT_PEOPLE_NAME'); ?></th>
        <th class="company"><?php echo TextHelper::_('COBALT_PEOPLE_COMPANY'); ?></th>
        <th class="owner" ><?php echo TextHelper::_('COBALT_PEOPLE_OWNER'); ?></th>
        <th class="email" ><?php echo TextHelper::_('COBALT_PEOPLE_EMAIL'); ?></th>
        <th class="phone" ><?php echo TextHelper::_('COBALT_PEOPLE_PHONE'); ?></th>
        <th class="status" ><?php echo TextHelper::_('COBALT_PEOPLE_STATUS'); ?></th>
        <th class="source" ><?php echo TextHelper::_('COBALT_PEOPLE_SOURCE'); ?></th>
        <th class="type" ><?php echo TextHelper::_('COBALT_PEOPLE_TYPE'); ?></th>
        <th class="notes" ><?php echo TextHelper::_('COBALT_PEOPLE_NOTES'); ?></th>
        <th class="address" ><?php echo TextHelper::_('COBALT_ADDRESS'); ?></th>
        <th class="added" ><?php echo TextHelper::_('COBALT_PEOPLE_ADDED'); ?></th>
        <th class="updated" ><?php echo TextHelper::_('COBALT_PEOPLE_UPDATED'); ?></th>
    </tr>
</thead>
