<?php
/**
 * Cobalt CRM
 *
 * @copyright  Copyright (C) 2012 - 2014 cobaltcrm.org All Rights Reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

defined('_CEXEC') or die;

use Cobalt\Helper\DealHelper;
use Cobalt\Helper\TextHelper;
use Cobalt\Helper\UsersHelper;
use Cobalt\Templating\TemplateReference;

// Available variables in this layout
/** @var \Symfony\Component\Templating\PhpEngine $view */
/** @var array $dealList */

$stages   = DealHelper::getStages(null, true, false);
$statuses = DealHelper::getStatuses(null, true);
$sources  = DealHelper::getSources(null);
$users    = UsersHelper::getUsers(null, true);
?>
<thead>
    <tr>
        <th class="checkbox_column"><input rel="tooltip" title="<?php echo TextHelper::_('COBALT_CHECK_ALL_ITEMS'); ?>" data-placement="bottom" type="checkbox" onclick="Cobalt.selectAll(this);" /></th>
        <th class="name" ><?php echo ucwords(TextHelper::_('COBALT_DEALS_NAME')); ?></th>
        <th class="company"><?php echo ucwords(TextHelper::_('COBALT_DEALS_COMPANY')); ?></th>
        <th class="amount" ><?php echo ucwords(TextHelper::_('COBALT_DEALS_AMOUNT')); ?></th>
        <th class="status" ><?php echo ucwords(TextHelper::_('COBALT_DEALS_STATUS')); ?></th>
        <th class="stage" ><?php echo ucwords(TextHelper::_('COBALT_DEALS_STAGE')); ?></th>
        <th class="source" ><?php echo ucwords(TextHelper::_('COBALT_DEAL_SOURCE')); ?></th>
        <th class="expected_close" ><?php echo ucwords(TextHelper::_('COBALT_DEALS_EXPECTED_CLOSE')); ?></th>
        <th class="actual_close" ><?php echo ucwords(TextHelper::_('COBALT_DEALS_ACTUAL_CLOSE')); ?></th>
        <th class="contacts" >&nbsp;</th>
    </tr>
</thead>
<tbody id="list">
<?php foreach ($dealList as $deal) : ?>
	<?php echo $view->render(new TemplateReference('entry', 'deals'), array('deal' => $deal, 'stages' => $stages, 'statuses' => $statuses, 'sources' => $sources, 'users' => $users)); ?>
<?php endforeach; ?>
</tbody>
