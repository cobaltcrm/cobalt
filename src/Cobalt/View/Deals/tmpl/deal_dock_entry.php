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
defined( '_CEXEC' ) or die( 'Restricted access' );

$deal = $this->deal; $k = $this->offset;
echo '<tr class="cobalt_row_'.$k.'">';
    echo '<td><a href="'.RouteHelper::_('index.php?view=deals&layout=deal&id='.$deal->id).'">'.$deal->name.'</a></td>';
    echo '<td>'.$deal->owner_first_name.' '.$deal->owner_last_name.'</td>';
    echo '<td><div class="deal-status-'.strtolower($deal->status_name).'"></div></td>';
    echo '<td><span class="amount">'.ConfigHelper::getConfigValue('currency').$deal->amount.'</span></td>';
echo '</tr>';
