<?php
/**
 * Cobalt CRM
 *
 * @copyright  Copyright (C) 2012 - 2014 cobaltcrm.org All Rights Reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

defined('_CEXEC') or die;

use Cobalt\Factory;
use Cobalt\Helper\RouteHelper;
use Cobalt\Helper\StylesHelper;
use Cobalt\Helper\TextHelper;

$app = Factory::getApplication();
$controller = $app->input->get('controller');
$view = $app->input->get('view');

// Load menu
$menu_model = Factory::getModel('Menu');
$list = $menu_model->getMenu();
?>

<div class="navbar navbar-default navbar-fixed-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<?php if (StylesHelper::getSiteLogo()) : ?>
			<div class="site-logo pull-left">
				<img id="site-logo-img" src="<?php echo StylesHelper::getSiteLogo(); ?>" />
			</div>
			<?php endif; ?>
			<a id="site-name-link" class="navbar-brand" href="<?php echo $app->get('uri.base.full'); ?>">
				<?php echo StylesHelper::getSiteName(); ?>
			</a>
		</div>
		<ul class="nav navbar-nav">
			<?php foreach ($list->menu_items as $name) : ?>
			<?php $class = $name == $controller || $name == $view ? 'active' : ''; ?>
			<li>
				<a class="<?php echo $class; ?>" href="<?php echo RouteHelper::_('index.php?view='.$name); ?>"><?php echo ucwords(TextHelper::_('COBALT_MENU_'.strtoupper($name))); ?></a>
			</li>
			<?php endforeach; ?>
		</ul>
		<ul class="nav navbar-nav navbar-right">
			<li data-toggle="tooltip" title="<?php echo TextHelper::_('COBALT_CREATE_ITEM'); ?>" data-placement="right" class="dropdown">
				<a class="feature-btn dropdown-toggle" data-toggle="dropdown" href="#" id="create_button">
					<i class="glyphicon glyphicon-plus-sign icon-white"></i>
				</a>
				<ul class="dropdown-menu">
					<li>
						<a href="<?php echo RouteHelper::_('index.php?view=companies&layout=edit&format=raw&tmpl=component'); ?>" data-target="#CobaltAjaxModal" data-toggle="modal">
							<i class="glyphicon glyphicon-plus-sign"></i> <?php echo ucwords(TextHelper::_('COBALT_NEW_COMPANY')); ?>
						</a>
					</li>
					<li>
						<a href="<?php echo RouteHelper::_('index.php?view=people&layout=edit&format=raw&tmpl=component'); ?>" data-target="#CobaltAjaxModal" data-toggle="modal">
							<i class="glyphicon glyphicon-plus-sign"></i> <?php echo ucwords(TextHelper::_('COBALT_NEW_PERSON')); ?>
						</a>
					</li>
					<li>
						<a href="<?php echo RouteHelper::_('index.php?view=deals&layout=edit&format=raw&tmpl=component'); ?>" data-target="#CobaltAjaxModal" data-toggle="modal">
							<i class="glyphicon glyphicon-plus-sign"></i> <?php echo ucwords(TextHelper::_('COBALT_NEW_DEAL')); ?>
						</a>
					</li>
					<li>
						<a href="<?php echo RouteHelper::_('index.php?view=goals&layout=add'); ?>">
							<i class="glyphicon glyphicon-plus-sign"></i> <?php echo ucwords(TextHelper::_('COBALT_NEW_GOAL')); ?>
						</a>
					</li>
				</ul>
			</li>
			<li data-toggle="tooltip" title="<?php echo TextHelper::_('COBALT_VIEW_PROFILE'); ?>" data-placement="bottom">
				<a class="block-btn" href="<?php echo RouteHelper::_('index.php?view=profile'); ?>">
					<i class="glyphicon glyphicon-user icon-white"></i>
				</a>
			</li>
			<li data-toggle="tooltip" title="<?php echo TextHelper::_('COBALT_SUPPORT'); ?>" data-placement="bottom">
				<a class="block-btn" href="http://www.cobaltcrm.org/" target="_blank">
					<i class="glyphicon glyphicon-question-sign icon-white"></i>
				</a>
			</li>
			<li data-toggle="tooltip" title="<?php echo TextHelper::_('COBALT_SEARCH'); ?>" data-placement="bottom">
				<a class="block-btn" href="#" onclick="Cobalt.showSiteSearch(); return false;">
					<i class="glyphicon glyphicon-search icon-white"></i>
				</a>
			</li>
			<?php if (UsersHelper::isAdmin()) : ?>
			<li data-toggle="tooltip" title="<?php echo TextHelper::_('COBALT_ADMINISTRATOR_CONFIGURATION'); ?>" data-placement="bottom">
				<a class="block-btn" href="<?php echo RouteHelper::_('index.php?view=cobalt'); ?>" >
					<i class="glyphicon glyphicon-cog icon-white"></i>
				</a>
			</li>
			<?php endif; ?>
			<?php if (UsersHelper::getLoggedInUser() && $app->input->get('view') != 'print') : ?>
			<?php $returnURL = base64_encode(RouteHelper::_('index.php?view=dashboard')); ?>
			<li data-toggle="tooltip" title="<?php echo TextHelper::_('COBALT_LOGOUT'); ?>" data-placement="bottom">
				<a class="block-btn" data-toggle="modal" href="#logoutModal">
					<i class="glyphicon glyphicon-off icon-white"></i>
				</a>
			</li>
			<?php endif; ?>
		</ul>
	</div>
	<div class="container">
		<div style="display:none;" class="pull-right col-xs-3" id="site_search">
			<form action="index.php" id="site_search_form">
				<input type="text" class="form-control site_search" name="site_search_input" id="site_search_input" placeholder="<?php echo TextHelper::_('COBALT_SEARCH_SITE'); ?>" value="" />
				<input type="hidden" name="view" />
				<input type="hidden" name="id" />
				<input type="hidden" name="layout" />
			</form>
		</div>
	</div>
</div>
