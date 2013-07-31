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
$app = JFactory::getApplication();

  for ( $i=0; $i<count($this->reports); $i++ ) {
      $report = $this->reports[$i];
      $k = $i%2; ?>
     <tr class="cobalt_row_<?php echo $k; ?>">
        <?php if ( $app->input->get('view') != "print" ) { ?>
         <td><input type="checkbox" name="ids[]" value="<?php echo $report['id']; ?>" /></td>
         <?php } ?>
         <td><a href="<?php echo JRoute::_("index.php?view=deals&layout=deal&id=".$report['id']); ?>"><?php echo $report['name']; ?></a></td>
         <td><?php echo $report['owner_first_name']." ".$report['owner_last_name']; ?></td>
         <td><?php echo CobaltHelperConfig::getCurrency().$report['amount']; ?></td>
         <td><?php echo $report['source_name']; ?></td>
         <td><?php echo $report['stage_name']; ?></td>
         <td><?php echo $report['percent']; ?>%</td>
         <td><div class="deal-status-<?php echo strtolower($report['status_name']); ?>"></div></td>
         <td><?php echo CobaltHelperDate::formatDate($report['expected_close']); ?></td>
         <td><?php echo CobaltHelperDate::formatDate($report['modified']); ?></td>
         <td><?php echo CobaltHelperDate::formatDate($report['created']); ?></td>
     </tr>
  <?php }
?>
<?php
    $filtered_amount = 0;
    if ( count($this->reports) > 0 ) {
    foreach ($this->reports as $key=>$report) {
        $filtered_amount += $report['amount'];
    }
}?>
<script type="text/javascript">
    var amount = <?php echo $filtered_amount; ?>;
    jQuery("#filtered_amount").html(amount);
</script>
