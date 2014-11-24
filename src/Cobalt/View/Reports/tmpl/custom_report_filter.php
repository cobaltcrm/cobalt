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

 $fields = unserialize($this->report[0]['fields']); ?>
<?php $rows = array(); ?>
    <?php foreach ($fields as $id => $text) { ?>
        <?php $rows[] = $id; ?>
    <?php } ?>
 <?php
for ( $i=0; $i<count($this->report_data); $i++ ) {
    $report = $this->report_data[$i];
    $k = $i%2; ?>
   <tr class="cobalt_row_<?php echo $k; ?>">
       <?php foreach ($rows as $row) { ?>
           <?php $custom_field = strstr($row,"custom_"); ?>
           <?php if (!$custom_field) { ?>
           <?php switch ($row) {
                  case "summary": ?>
                      <td><?php echo $report['summary']; ?></td>
                  <?php break;
                  case "status_id": ?>
                      <td><div class="deal-status-<?php echo strtolower($report['status_name']); ?>"></div></td>
                  <?php break;
                  case "modified": ?>
                   <td><?php echo DateHelper::formatDate($report['modified']); ?></td>
                  <?php break;
                  case "expected_close": ?>
                      <td><?php echo DateHelper::formatDate($report['expected_close']); ?></td>
                  <?php break;
                  case "actual_close": ?>
                      <td><?php echo DateHelper::formatDate($report['actual_close']); ?></td>
                  <?php break;
                  case "source_id": ?>
                      <td><?php echo $report['source_name']; ?></td>
                  <?php break;
                  case "created": ?>
                      <td><?php echo DateHelper::formatDate($report['created']); ?></td>
                  <?php break;
                  case "primary_contact_name": ?>
                      <td><?php echo $report['primary_contact_first_name'] . ' ' . $report['primary_contact_last_name']; ?></td>
                  <?php break;
                  case "primary_contact_email": ?>
                      <td><?php echo $report['primary_contact_email']; ?></td>
                  <?php break;
                  case "primary_contact_phone"; ?>
                      <td><?php echo $report['primary_contact_phone']; ?></td>
                  <?php break;
                  case "primary_contact_city"; ?>
                      <td></td>
                  <?php break;
                  case "primary_contact_state"; ?>
                      <td></td>
                  <?php break;
                  case "primary_contact_company_name"; ?>
                       <td><?php echo $report['primary_contact_company_name']; ?></td>
                  <?php break;
                  case "owner_id": ?>
                       <td><?php echo $report['first_name'] . ' ' . $report['last_name']; ?></td>
                  <?php break;
                  case "name": ?>
                      <td><?php echo $report['name']; ?></td>
                  <?php break;
                  case "stage_id" : ?>
                      <td><?php echo $report['stage_name']; ?></td>
                  <?php break;
                  case "amount" : ?>
                      <td><?php echo ConfigHelper::getCurrency().$report['amount']; ?></td>
                  <?php break;
                  case "probability" ?>
                      <td><?php echo $report['probability']; ?>%</td>
                  <?php break;
                  case "company_name" ?>
                      <td><?php echo $report['company_name']; ?></td>
                  <?php break; ?>
           <?php } ?>
           <?php } else { ?>
                      <td><?php echo DropdownHelper::getCustomValue("deal",$custom_field,$report[$custom_field],$report['id']); ?></td>
           <?php } ?>
       <?php } ?>
   </tr>
<?php }
