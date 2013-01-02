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

<script type="text/javascript">
	var loc = "dashboard";
</script>

	<div data-role='header' data-theme='b'>
		<h1><?php echo CRMText::_('COBALT_DASHBOARD_HEADER'); ?></h1>
		<a data-icon='delete' data-role='button' href='<?php echo JRoute::_('index.php?option=com_users&task=logout'); ?>' rel='external'>
			<?php echo CRMText::_('COBALT_LOGOUT'); ?>
		</a>
	</div>

	<div data-role='content' data-theme='b'><h2><?php echo CRMText::_('COBALT_WELCOME').' '.ucwords($this->first_name); ?>!</h2>
		<ul data-inset='true' data-role='listview'>
			<li data-theme="c">
				<a href="<?php echo JRoute::_('index.php?view=events'); ?>">
				<img class='ui-li-icon' src='<?php echo JURI::root(); ?>//media/images/mobile/agenda.png' />
				<?php echo ucwords(CRMText::_('COBALT_AGENDA')); ?>
				<span class='ui-li-count'><?php echo $this->numEvents; ?></span>
				</a>
			</li>
			<li data-theme="c">
				<a href="<?php echo JRoute::_('index.php?view=deals'); ?>">
					<img class='ui-li-icon' src='<?php echo JURI::root(); ?>//media/images/mobile/deals.png' />
					<?php echo ucwords(CRMText::_('COBALT_DEALS_HEADER')); ?>
					<span class='ui-li-count'><?php echo $this->numDeals; ?></span>
				</a>
			</li>
			<li data-theme="c">
				<a href="<?php echo JRoute::_('index.php?view=people&type=leads'); ?>">
					<img class='ui-li-icon' src='<?php echo JURI::root(); ?>//media/images/mobile/leads.png' />
					<?php echo ucwords(CRMText::_('COBALT_LEADS')); ?>
					<span class='ui-li-count'><?php echo $this->numLeads; ?></span>
				</a>
			</li>
			<li data-theme="c">
				<a href="<?php echo JRoute::_('index.php?view=people&type=not_leads'); ?>">
					<img class='ui-li-icon' src='<?php echo JURI::root(); ?>//media/images/mobile/contacts.png' />
					<?php echo ucwords(CRMText::_('COBALT_CONTACTS')); ?>
					<span class='ui-li-count'><?php echo $this->numContacts; ?></span>
				</a>
			</li>
			<li data-theme="c">
				<a href="<?php echo JRoute::_('index.php?view=companies'); ?>">
					<img class='ui-li-icon' src='<?php echo JURI::root(); ?>//media/images/mobile/companies.png' />
					<?php echo ucwords(CRMText::_('COBALT_COMPANIES')); ?>
					<span class='ui-li-count'><?php echo $this->numCompanies; ?></span>
				</a>
			</li>
		</ul>
		<div style="float:right;font-size:8px;"><a href="javascript:void(0);" onclick="window.location='<?php echo JRoute::_('index.php?view=dashboard&mobile=no'); ?>';"><?php echo CRMText::_('COBALT_TOGGLE_DESKTOP_VIEW'); ?></a></div>
	</div>