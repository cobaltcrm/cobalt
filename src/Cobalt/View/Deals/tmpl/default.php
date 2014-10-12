<?php
/**
 * Cobalt CRM
 *
 * @copyright  Copyright (C) 2012 - 2014 cobaltcrm.org All Rights Reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

defined('_CEXEC') or die;

use Cobalt\Helper\RouteHelper;
use Cobalt\Helper\TemplateHelper;
use Cobalt\Helper\TextHelper;
use Cobalt\Helper\UsersHelper;
use Cobalt\Templating\TemplateReference;
use Joomla\Filter\OutputFilter;

// Available variables in this layout
/** @var \Symfony\Component\Templating\PhpEngine $view */
/** @var array $closed_stages */
/** @var array $dealList */
/** @var string $total_deals */
/** @var \stdClass $deal */
/** @var array $deal_types */
/** @var string $deal_type_name */
/** @var string $user_id */
/** @var string $member_role */
/** @var array $teams */
/** @var array $users */
/** @var array $stages */
/** @var string $stage_name */
/** @var string $user_name */
/** @var array $closing_names */
/** @var string $closing_name */
/** @var \Joomla\Registry\Registry $state */
/** @var array $column_filters */
/** @var array $selected_columns */
/** @var array $dataTableColumns */
/** @var string $deal_filter */

$view->extend('index');
$view['assets']->addScriptDeclaration('var loc = "deals";');
$view['assets']->addScriptDeclaration('var order_url = "index.php?view=deals&layout=list&format=raw&tmpl=component";');
$view['assets']->addScriptDeclaration('var order_dir = "' . $state->get('Deal.filter_order_Dir') . '";');
$view['assets']->addScriptDeclaration('var order_col = "' . $state->get('Deal.filter_order') . '";');
$view['assets']->addScriptDeclaration('var order_url = "' . json_encode($dataTableColumns) . '";');
?>

<div class="page-header">
	<div class="modal fade" id="dealModal" tabindex="-1" role="dialog" aria-labelledby="dealModal" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content"></div>
		</div>
	</div>
	<div class="pull-right btn-group">
		<?php if (UsersHelper::canExport()): ?>
			<button type="button" href="index.php?view=deals&layout=edit&format=raw&tmpl=component" data-target="#dealModal" data-toggle="modal" class="btn btn-default">
				<i class="glyphicon glyphicon-plus icon-white"></i>
				<?php echo TextHelper::_('COBALT_ADD_DEALS'); ?>
			</button>
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
				<span class="caret"></span>
				<span class="sr-only">Toggle Dropdown</span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<li>
					<a href="<?php echo RouteHelper::_('index.php?view=import&import_type=deals'); ?>">
						<i class="glyphicon glyphicon-arrow-up"></i> <?php echo TextHelper::_('COBALT_IMPORT_DEALS'); ?>
					</a>
				</li>
				<li>
					<a href="javascript:void(0)" onclick="Cobalt.exportCSV()">
						<i class="glyphicon glyphicon glyphicon-arrow-down"></i> <?php echo TextHelper::_('COBALT_EXPORT_DEALS'); ?>
					</a>
				</li>
			</ul>
		<?php else: ?>
			<button data-placement="bottom" type="button" href="index.php?view=deals&layout=edit&format=raw&tmpl=component" data-target="#dealModal" data-toggle="modal" class="btn btn-default">
				<i class="glyphicon glyphicon-plus icon-white"></i>
				<?php echo TextHelper::_('COBALT_ADD_DEALS'); ?>
			</button>
			<button data-placement="bottom" onclick="location.href = '<?php echo RouteHelper::_('index.php?view=import&import_type=deals'); ?>';" type="button" class="btn btn-default">
				<i class="glyphicon glyphicon-arrow-up"></i>
				<?php echo TextHelper::_('COBALT_IMPORT_DEALS'); ?>
			</button>
		<?php endif; ?>
	</div>
	<h1><?php echo ucwords(TextHelper::_('COBALT_DEALS_HEADER')); ?></h1>
</div>
<ul class="list-inline filter-sentence">
	<li><span><?php echo TextHelper::_('COBALT_SHOW'); ?></span></li>
	<li class="dropdown">
		<a class="dropdown-toggle update-toggle-text" href="#" data-toggle="dropdown" role="button" id="deal_type_link">
                <span class="dropdown-label"><?php echo $deal_type_name; ?><span>
		</a>
		<ul class="dropdown-menu" role="menu" aria-labelledby="deal_type_link" data-filter="item">
			<?php foreach ($deal_types as $title => $text) : ?>
				<li>
					<a href="#" class="filter_<?php echo OutputFilter::stringURLUnicodeSlug($title); ?>" data-filter-value="<?php echo $title; ?>">
						<?php echo $text; ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</li>
	<li><span><?php echo TextHelper::_('COBALT_FOR'); ?></span></li>
	<li class="dropdown">
		<a class="dropdown-toggle update-toggle-text" href="#" data-toggle="dropdown" role="button" id="deal_user_link">
			<span class="dropdown-label"><?php echo $user_name; ?><span>
		</a>
		<ul class="dropdown-menu" role="menu" aria-labelledby="deal_user_link" data-filter="ownertype">
			<li>
				<a class="filter_user_<?php echo $user_id ?>" data-filter-value="member:<?php echo $user_id; ?>">
					<?php echo TextHelper::_('COBALT_ME'); ?>
				</a>
			</li>
			<?php if ($member_role != 'basic') : ?>
				<li>
					<a href="#" class="filter_user_all" data-filter-value="all">
						<?php echo TextHelper::_('COBALT_ALL_USERS'); ?>
					</a>
				</li>
			<?php endif; ?>
			<?php if ($member_role == 'exec') :
				if (count($teams) > 0) :
					foreach ($teams as $team) : ?>
						<li>
							<a href="#" class="filter_team_<?php echo $team['team_id']; ?>" data-filter-value="team:<?php echo $team['team_id']; ?>">
								<?php echo $team['team_name'] . TextHelper::_('COBALT_TEAM_APPEND'); ?>
							</a>
						</li>
					<?php endforeach;
				endif;
			endif;
			if (count($users) > 0) :
				foreach ($users as $user) : ?>
					<li>
						<a href="#" class="filter_user_<?php echo $user['id']; ?>" data-filter-value="member:<?php echo $user['id']; ?>">
							<?php echo $user['first_name'] . "  " . $user['last_name']; ?>
						</a>
					</li>
				<?php endforeach;
			endif; ?>
		</ul>
	</li>
	<li>
		<span><?php echo TextHelper::_('COBALT_IN'); ?></span>
	</li>
	<li class="dropdown">
		<a class="dropdown-toggle update-toggle-text" href="#" data-toggle="dropdown" role="button" id="deal_stages_link">
			<span class="dropdown-label"><?php echo $stage_name; ?></span>
		</a>
		<ul class="dropdown-menu" role="menu" aria-labelledby="deal_stages_link" data-filter="stage">
			<?php foreach ($stages as $title => $text) : ?>
				<li>
					<a href="#" class="filter_<?php echo OutputFilter::stringURLUnicodeSlug($title); ?>" data-filter-value="<?php echo $title; ?>">
						<?php echo $text; ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</li>
	<li>
		<span><?php echo TextHelper::_('COBALT_CLOSING'); ?></span>
	</li>
	<li class="dropdown">
		<a class="dropdown-toggle update-toggle-text" href="#" data-toggle="dropdown" role="button" id="deal_closing_link">
			<span class="dropdown-label"><?php echo $closing_name; ?></span>
		</a>
		<ul class="dropdown-menu" role="menu" aria-labelledby="deal_closing_link" data-filter="closing">
			<?php foreach ($closing_names as $title => $text) : ?>
				<li>
					<a href="#" class="filter_<?php echo OutputFilter::stringURLUnicodeSlug($title); ?>" data-filter-value="<?php echo $title; ?>">
						<?php echo $text; ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</li>
	<li>
		<?php echo TextHelper::_('COBALT_NAMED'); ?>
	</li>
	<li>
		<input name="deal_name" type="text" placeholder="<?php echo TextHelper::_('COBALT_ANYTHING'); ?>" value="<?php echo $deal_filter; ?>" class="form-control datatable-searchbox">
	</li>
	<li>
		<div class="ajax_loader"></div>
	</li>
</ul>

<?php echo TemplateHelper::getListEditActions(); ?>

<form method="post" id="list_form" action="<?php echo RouteHelper::_('index.php?view=deals'); ?>">
	<table class="table table-hover table-striped data-table table-bordered" id="deals">
		<?php echo $view->render(new TemplateReference('list', 'deals'), array('dealList' => $dealList)); ?>
	</table>
	<input type="hidden" name="list_type" value="deals"/>
</form>
