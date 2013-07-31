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
?>
<tbody id="latest_activities">
    <?php
    $n = count($this->activity);
    for ($i=0;$i<$n;$i++) {
        $activity = $this->activity[$i];
        $k = $i%2;
        if ($activity->type=="notes") {
            $name_type = $activity->type.'_name';
            $name = TextHelper::_("COBALT_NOTE");
            $link = "<a href='javascript:void(0);' onclick='editNoteEntry(".$activity->type_id.")'>";
        } elseif ($activity->type=="event") {
            $name_type = $activity->type.'_name';
            $name = array_key_exists($name_type, $activity) ? $activity->$name_type : false;
            $link = "<a href='javascript:void(0);' onclick='editEvent(".$activity->type_id.",\"event\")'>";

        } elseif ($activity->type=="note") {

            $name_type = $activity->type.'_name';
            $name = array_key_exists($name_type, $activity) ? $activity->$name_type : false;
            $link = "<a href='javascript:void(0);' onclick='editNoteEntry(".$activity->type_id.")'>";

        } else {
            $name_type = $activity->type.'_name';
            $name = array_key_exists($name_type, $activity) ? $activity->$name_type : false;

            switch ($activity->type) {
                case "company":
                    $view = "view=companies&layout=company";
                break;
                case "deal":
                    $view = "view=deals&layout=deal";
                break;
                case "person":
                    $view = "view=people&layout=person";
                break;
                case "report":
                    $view = "view=reports";
                break;
                case "document":
                    $view = "view=documents";
                break;
                case "goal":
                    $view = "view=goals";
                break;
                default:
                    $view = "";
                break;
            }

            $link = "<a href=".JRoute::_('index.php?'.$view.'&id='.$activity->type_id).">";
        }

        if ($name) {
            if (stripos($activity->field,'_id')!== false) {
                    $old_field_value = $activity->type.'_'.str_replace('_id', '_name_old' , $activity->field);
                    $old_value = isset($activity->$old_field_value) ? $activity->$old_field_value : "";
                    if ($old_value=="") { $old_value = TextHelper::_('COBALT_NOTHING');}
                    $new_field_value = $activity->type.'_'.str_replace('_id', '_name' , $activity->field);
                    $new_value = isset($activity->$new_field_value) ? $activity->$new_field_value : "";
            } else {
                    $old_value = $activity->old_value;
                    $new_value = $activity->new_value;
            }

            $dates = array('due_date','modified');
            if (in_array($activity->field,$dates)) {
                $old_value = CobaltHelperDate::formatDate($activity->old_value,true);
                $new_value = CobaltHelperDate::formatDate($activity->new_value,true);
            }
    ?>
    <?php if ($new_value != "") { ?>
        <tr class="crmery_row_<?php echo $k; ?>">
            <td><?php echo $link.$name; ?></a></td>
            <td><?php echo JText::sprintf('COBALT_ACTIVITY_'.strtoupper($activity->action_type),ucwords(str_replace('_',' ',$activity->type)), ucwords(str_replace(' id','',str_replace(' 1','',str_replace('_',' ',$activity->field)))),$old_value,$new_value); ?></td>
            <td><?php echo $activity->owner_name; ?></td>
            <td><?php echo CobaltHelperDate::getElapsedTime($activity->date,true,true,true,true,true); ?></td>
        </tr>
    <?php } ?>
    <?php } } ?>
</tbody>
