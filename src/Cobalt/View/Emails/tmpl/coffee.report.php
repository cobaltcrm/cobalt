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
$uri = Factory::getApplication()->get('uri');
?>

<table width="600" cellpadding="0" cellspacing="10" border="0" align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:11px;">
    <tr>
        <td width="300" height="60" valign="top">
            <a href="<?php echo $uri->base(); ?>"><?php echo $uri->base(); ?></a>
        </td>
        <td width="300" height="60" valign="top" align="right">
      <p><strong><?php echo TextHelper::_('COBALT_MORNING_COFFEE_REPORT_TITLE') ?> <?php echo $this->user->first_name.' '.$this->user->last_name; ?><br><?php echo TextHelper::_('COBALT_SUMMARY_FOR'); ?> <?php echo DateHelper::formatDate(date('Y-m-d')); ?></strong> <br>
        <a href="<?php echo JURI::base().'/index.php?option=com_user&view=login'; ?>" style="color:#999999;"><?php echo TextHelper::_('COBALT_LOGIN'); ?></a>      </p>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <table width="100%" border="0" cellpadding="5" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:11px;">
                <tr>
                    <td style="font-size:14px; font-weight: bold; border-bottom: 1px solid #cccccc; color: #1E759E;" colspan="2"><?php echo ucwords(TextHelper::_('COBALT_SALES_PIPELINE')); ?></td>
                </tr>
                <tr>
                    <td style="color: #039718; font-weight: bold; font-size: 14px;"><?php echo ucwords(TextHelper::_('COBALT_ACTIVE_DEALS')); ?></td>
                     <td width="75" align="right" style="color: #039718; font-weight: bold; font-size: 14px;" id="sales_pipeline_total"><?php echo ConfigHelper::getCurrency(); ?><?php echo $this->totalDealsAmount; ?></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td valign="top" width="50%">
            <table width="100%" border="0" cellpadding="5" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:11px;">
                <tr>
                    <td style="font-size:14px; font-weight: bold; border-bottom: 1px solid #cccccc; color: #1E759E;" colspan="2"><?php echo ucwords(TextHelper::_('COBALT_DEALS')); ?></td>
                </tr>
                <?php
                $n = count($this->stages);
                if ($n > 0) { foreach ($this->stages as $stage) { ?>
                <tr>
                    <td style="color: #<?php echo $stage['color']; ?>;"><?php echo $stage['name']; ?>
                        <br /><a href="<?php echo \Cobalt\Helper\LinkHelper::viewDeals(array('stage' => $stage['id'])); ?>" style="color:#999999;"><?php echo ucwords(JText::sprintf('COBALT_VIEW_DEALS',ucwords($stage['name']))); ?></a>
                    </td>
                      <td width="75" align="right" style="color: #<?php echo $stage['color']; ?>;" id="<?php echo $stage['name']; ?>_today">
                          <?php echo ConfigHelper::getCurrency(); ?><?php echo $stage['amount']; ?>
                      </td>
                  </tr>
                  <?php } } ?>
            </table>
        </td>
        <td valign="top" width="50%">
            <table width="100%" border="0" cellpadding="5" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:11px;">
                <tr>
                    <td style="font-size:14px; font-weight: bold; border-bottom: 1px solid #cccccc; color: #1E759E;" colspan="2"><?php echo ucwords(TextHelper::_('COBALT_LEADS')); ?></td>
                </tr>
                <tr>
                    <td style="color: #039718;"><?php echo ucwords(TextHelper::_('COBALT_CONVERTED_TO_CONTACTS')); ?></td>
                     <td align="right" style="color: #039718;"><?php echo $this->numConvertedLeads; ?></td>
                </tr>
                <tr>
                    <td style="color:#CB6A13;"><?php echo ucwords(TextHelper::_('COBALT_NEW')); ?></td>
                      <td align="right" style="color:#CB6A13;"><?php echo $this->numNewLeads; ?></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td valign="top" width="50%">
            <table width="100%" border="0" cellpadding="5" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:11px;">
                <tr>
                    <td style="font-size:14px; font-weight: bold; border-bottom: 1px solid #cccccc; color: #1E759E;" colspan="2"><?php echo TextHelper::_('COBALT_NOTES_WRITTEN'); ?></td>
                </tr>
                <?php
                $c = count($this->notes);
                $totalNotes = 0;
                if ($c > 0) {
                    foreach ($this->notes as $category_name => $category_total) {
                        $totalNotes += $category_total;
                ?>
                <tr>
                  <td class='cat_name'><?php echo $category_name; ?></td>
                  <td class='cat_count'><?php echo $category_total; ?></td>
                </tr>
                <?php } } ?>
                <tr>
                    <td><?php echo TextHelper::_('COBALT_TOTAL'); ?></td>
                     <td align="right" id="cat_total"><?php echo $totalNotes; ?></td>
                </tr>
            </table>
        </td>
        <td valign="top" width="50%">
            <table border='0' cellpadding='5' cellspacing='0' style='font-family:Arial, Helvetica, sans-serif; font-size:11px;' width='100%'>
                <tr>
                    <td colspan='2' style='font-size:14px; font-weight: bold; border-bottom: 1px solid #cccccc; color: #1E759E;'><?php echo ucwords(TextHelper::_('COBALT_TODOS_COMPLETED')); ?></td>
                </tr>
                <?php
                $c = count($this->todos);
                $todoCompletedTodos = $totalNewTodos = 0;
                if ($c > 0) {
                    foreach ($this->todos as $todoCat_name => $todoCat_info) {
                            $todoCompletedTodos += $todoCat_info->completed;
                            $totalNewTodos += $todoCat_info->total;
                            $totalNewTodos -= $todoCat_info->completed;
                ?>
                    <tr>
                        <td><?php echo $todoCat_name; ?></td>
                        <td><?php echo $todoCat_info->total; ?></td>
                    </tr>
                <?php } } ?>
                <tr>
                    <td><?php echo ucwords(TextHelper::_('COBALT_TOTAL_NEW')); ?></td>
                    <td align='right'><?php echo $totalNewTodos; ?></td>
                </tr>
                <tr>
                    <td><?php echo ucwords(TextHelper::_('COBALT_TOTAL_COMPLETED')); ?></td>
                    <td align='right'><?php echo $todoCompletedTodos; ?></td>
                </tr>
            </table>
        </td>
    </tr>
    <?php /**
    <tr>
        <td style="font-size:14px; font-weight: bold; border-bottom: 1px solid #cccccc; color: #1E759E;" colspan="2"><?php echo TextHelper::_('COBALT_DEAL_ACTIVITY'); ?></td>
    </tr>
    <tr>
        <td>
            <table border='0' cellpadding='5' cellspacing='0' style='font-family:Arial, Helvetica, sans-serif; font-size:11px;' width='100%'>
            <?php
            $d = count($this->dealActivity);
            for ($i=0;$i<$d;$i++) {
                $da = $this->dealActivity[$i];
            ?>
                <tr>
                      <td><a href="<?php echo JURI::base().'/index.php?view=deals&id='.$da->deal_id; ?>"><?php echo $da->deal_name; ?></a></td>
                      <td><?php echo TextHelper::_('COBALT_ACTIVITY_UPDATED',$da->field,$da->old_value,$da->new_value); ?></td>
                </tr>
            <?php } ?>
            </table>
        </td>
    </tr>
    <tr>
        <td style="font-size:14px; font-weight: bold; border-bottom: 1px solid #cccccc; color: #1E759E;" colspan="2"><?php echo TextHelper::_('COBALT_LEADS_ACTIVITY'); ?></td>
    </tr>
    <tr>
        <td>
            <table border='0' cellpadding='5' cellspacing='0' style='font-family:Arial, Helvetica, sans-serif; font-size:11px;' width='100%'>
            <?php
            $l = count($this->leadActivity);
            for ($i=0;$i<$l;$i++) {
                $la = $this->leadActivity[$i];
            ?>
                <tr>
                      <td><a href="<?php echo JURI::base().'/index.php?view=people&id='.$la->lead_id; ?>"><?php echo $la->first_name.' '.$la->last_name; ?></a></td>
                      <td><?php echo TextHelper::_('COBALT_ACTIVITY_UPDATED',$la->field,$la->old_value,$la->new_value); ?></td>
                </tr>
            <?php } ?>
            </table>
        </td>
    </tr>
    <tr>
        <td style="font-size:14px; font-weight: bold; border-bottom: 1px solid #cccccc; color: #1E759E;" colspan="2"><?php echo TextHelper::_('COBALT_CONTACTS_ACTIVITY'); ?></td>
    </tr>
    <tr>
        <td>
            <table border='0' cellpadding='5' cellspacing='0' style='font-family:Arial, Helvetica, sans-serif; font-size:11px;' width='100%'>
            <?php
            $c = count($this->contactActivity);
            for ($i=0;$i<$c;$i++) {
                $ca = $this->contactActivity[$i];
            ?>
                <tr>
                      <td><a href="<?php echo JURI::base().'/index.php?view=people&id='.$ca->contact_id; ?>"><?php echo $ca->first_name.' '.$ca->last_name; ?></a></td>
                      <td><?php echo TextHelper::_('COBALT_ACTIVITY_UPDATED',$ca->field,$ca->old_value,$ca->new_value); ?></td>
                </tr>
            <?php } ?>
            </table>
        </td>
    </tr>
     **/ ?>
</table>
