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

<p>
	<?php echo JText::sprintf('COBALT_ADMIN_USE_EMAIL_HEADER',CobaltHelperDate::formatDate($starting_date),CobaltHelperDate::formatDate($ending_date)); ?>
</p>

<table>
	<tbody>
		
		<?php if($this->teammates) > 0 ) { foreach($this->teammates as $teammate) {  ?>
		<tr>
			<td><?php echo CRMText::_('COBALT_TEAMMATE'); ?></td>
			<td><?php  echo $teammate->name; ?></td>
		</tr>
		<tr>
			<td><?php echo CRMText::_('COBALT_LOGINS'); ?></td>
			<td><?php echo $teammate->logins; ?></td>
		</tr>
		<tr>
			<td><?php echo CRMText::_('COBALT_PIPELINE_SIZE'); ?></td>
			<td><?php echo $teammate->pipeline; ?></td>
		</tr>
		<?php if($teammate->logged_in) { ?>
		<tr>
			<td colspan="2">
				<table style="border: 1px solid #DDDDDD;">
					<thead>
						<tr>
							<th style="background:#e6f4f7; padding:10px; text-transform: uppercase; font-size: 85%; color: #6793a7;">&nbsp;</th>
							<th style="background:#e6f4f7; padding:10px; text-transform: uppercase; font-size: 85%; color: #6793a7;"><?php echo ucwords(CRMText::_('COBALT_NEW_THIS_WEEK')); ?></th>
							<th style="background:#e6f4f7; padding:10px; text-transform: uppercase; font-size: 85%; color: #6793a7;"><?php echo ucwords(CRMText::_('COBALT_TOTAL')); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><?php echo ucwords(CRMText::_('COBALT_LEADS')); ?></td>
							<td><?php echo $teammate->weekly_leads; ?></td>
							<td><?php echo $teammate->total_leads; ?></td>
						</tr>
						<tr>
							<td><?php echo ucwords(CRMText::_('COBALT_CONTACTS')); ?></td>
							<td><?php echo $teammate->weekly_contacts; ?></td>
							<td><?php echo $teammate->total_contacts; ?></td>
						</tr>
						<tr>
							<td><?php echo ucwords(CRMText::_('COBALT_DEALS')); ?></td>
							<td><?php echo $teammate->weekly_deals; ?></td>
							<td><?php echo $teammate->total_deals; ?></td>
						</tr>
						<tr>
							<td><?php echo ucwords(CRMText::_('COBALT_NOTES')); ?></td>
							<td><?php echo $teammate->weekly_notes; ?></td>
							<td><?php echo $teammate->total_notes; ?></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<?php } ?>

		<?php } } ?>
	</tbody>
</table>