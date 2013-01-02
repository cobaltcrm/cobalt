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
	var loc = "events";
</script>


	<div data-role='header' data-theme='b'>
		<h1><?php echo CRMText::_('COBALT_ACTIVITY_HEADER'); ?></h1>
			<a href="<?php echo JRoute::_('index.php?view=dashboard'); ?>" data-icon="back" class="ui-btn-left">
				<?php echo CRMText::_('COBALT_BACK'); ?>
			</a>
			<a href="<?php echo JRoute::_('index.php?view=events&layout=edit_task'); ?>" data-icon="plus" class="ui-btn-right">
				<?php echo ucwords(CRMText::_('COBALT_ADD_TASK')); ?>
			</a>
	</div>

	<div data-role="content">	
		<ul class="ui-listview" data-role="listview" data-filter="true" data-autodividers="true" data-theme="c">
			<?php
			$n = count($this->events);
			for ( $i=0; $i<$n; $i++ ){
				$event = $this->events[$i];
				$event_date = CobaltHelperDate::formatDate($event['due_date']);
				$k = $i%2;				

					if($i==0 || substr_compare($event['due_date'],$this->events[$i-1]['due_date'],0)) { 
						echo "<li data-role='list-divider'>".CobaltHelperDate::getRelativeDate($event_date)." ".$event_date."</li>";
					}
				?>
					<li data-filtertext="<?php echo $event['name']; ?>">
						<a href="<?php echo JRoute::_('index.php?view=events&layout=event&id='.$event['id']); ?>">
							<h3 class="ui-li-heading"><?php echo $event['name']; ?></h3>
							<p class="ui-li-desc"><?php echo ucwords($event['type']); ?></p>
						</a>
					</li>
				<?php } ?>
		</ul>
	</div>