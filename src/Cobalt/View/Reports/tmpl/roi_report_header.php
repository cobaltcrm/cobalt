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

<table class="table table-striped table-hover">
        <thead>
            <th class="checkbox_column"><input type="checkbox" onclick="selectAll(this);" /></th>
            <th><div class="sort_order"><a class="s.name" onclick="sortTable('s.name',this)"><?php echo TextHelper::_('COBALT_REPORTS_SOURCE'); ?></a></div></th>
            <th><div class="sort_order"><a class="count(d.id)" onclick="sortTable('count(d.id)',this)"><?php echo TextHelper::_('COBALT_NUMBER_OF_DEALS'); ?></a></div></th>
            <th><div class="sort_order"><a class="sum(d.amount)" onclick="sortTable('sum(d.amount)',this)"><?php echo TextHelper::sprintf('COBALT_REVENUE',ConfigHelper::getConfigValue('currency')); ?></a></div></th>
            <th><div class="sort_order"><a class="s.cost" onclick="sortTable('s.cost',this)"><?php echo TextHelper::sprintf('COBALT_TOTAL_COSTS',ConfigHelper::getConfigValue('currency')); ?></a></div></th>
            <th><div class="sort_order"><a class="roi" onclick="sortTable('roi',this)"><?php echo TextHelper::_('COBALT_RETURN_ON_INVESTMENTS'); ?></a></div></th>
        </thead>
        <tbody class="results" id="reports">
