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

$deal = $this->info[0];
?>

<?php if ( array_key_exists('contact_info',$this) ||  array_key_exists('event_dock',$this) ) { ?>
<div class="rightColumn">
    <?php if ( array_key_exists('contact_info',$this) ) { ?>
    <div class="infoContainer">
            <h2><?php echo ucwords(CRMText::_('COBALT_CONTACT_INFO')); ?></h2>
            <?php echo $this->contact_info->render(); ?>
    </div>
    <?php } ?>
    <?php if ( array_key_exists('event_dock',$this) ) { ?>
    <div class="infoContainer" id='event_dock'>
        <h2><?php echo ucwords(CRMText::_('COBALT_TASKS_AND_EVENTS')); ?></h2>
        <?php echo $this->event_dock->render(); ?>
    </div>
    <?php } ?>
</div>
<?php } ?>

<div class="leftColumn">

<h1><?php echo $deal['name']; ?></h1>

<div class="container">
    <div class="columncontainer">
        <div class="threecolumn">
            <div class="small_info first">
                <?php echo CRMText::_('COBALT_EDIT_AMOUNT'); ?>:
                <span class="amount">
                    <?php echo CobaltHelperConfig::getCurrency(); ?>
                    <div class="inline" id="editable_amount"><?php echo $deal['amount']; ?></div>
                </span>
            </div>
            <div class="cobaltRow top">
                <div class="cobaltField"><?php echo ucwords(CRMText::_('COBALT_EDIT_COMPANY')); ?></div>
                <div class="cobaltValue"><?php echo $deal['company_name']; ?></div>
            </div>
            <div class="cobaltRow">
                <div class="cobaltField"><?php echo CRMText::_('COBALT_EDIT_OWNER'); ?></div>
                <div class="cobaltValue">
                    <?php echo $deal['owner_first_name']." ".$deal['owner_last_name']; ?>
                </div>
            </div>
        </div>
        <div class="threecolumn">
            <div class="small_info middle">
                <?php echo CRMText::_('COBALT_EDIT_AGE'); ?>:
                    <?php
                        echo CobaltHelperDate::getElapsedTime($deal['created']);
                    ?>
            </div>
            <div class="cobaltRow top">
                <div class="cobaltField"><?php echo CRMText::_('COBALT_EDIT_STAGE'); ?></div>
                <div class="cobaltValue">
                    <?php echo $deal['stage_name']; ?>
                </div>
            </div>
            <div class="cobaltRow">
                <div class="cobaltField"><?php echo CRMText::_('COBALT_EDIT_PROBABILITY'); ?></div>
                <div class="cobaltValue">
                    <div class="inline">
                        <?php if ( array_key_exists('probability',$deal) && $deal['probability'] != 0 ) {
                                echo $deal['probability'].'%';
                            } else {
                                echo CRMText::_('COBALT_NOT_SET');
                            } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="threecolumn">
            <div class="small_info last">
                <?php echo CRMText::_('COBALT_EXP_CLOSE'); ?>:
                    <?php echo CobaltHelperDate::formatDate($deal['expected_close']); ?>
            </div>
            <div class="cobaltRow top">
                <div class="cobaltField"><?php echo CRMText::_('COBALT_EDIT_STATUS'); ?></div>
                <div class="cobaltValue">
                    <?php if ( array_key_exists('status_id',$deal) && $deal['status_id'] != 0 ) {
                            echo "<div class='deal-status-".strtolower($deal['status_name'])."'></div>";
                        } else {
                            echo CRMText::_('COBALT_NOT_SET');
                        } ?>
                </div>
            </div>
            <div class="cobaltRow">
                <div class="cobaltField"><?php echo CRMText::_('COBALT_EDIT_SOURCE'); ?></div>
                <div class="cobaltValue">
                    <?php if ( array_key_exists('source_id',$deal) && $deal['source_id'] != 0 ) {
                            echo $deal['source_name'];
                        } else {
                            echo CRMText::_('COBALT_NOT_SET');
                        } ?>
                </div>
            </div>
    </div>

    <h2><?php echo CRMText::_('COBALT_EDIT_SUMMARY'); ?></h2>

    <div class="large_info">
            <?php if ( !array_key_exists('summary',$deal) || $deal['summary'] == "" || is_null($deal['summary']) ) {
                    echo CRMText::_('COBALT_NOT_SET');
                 } else {
                    echo $deal['summary'];
                } ?>
    </div>

    <h2><?php echo CRMText::_('COBALT_EDIT_NOTES'); ?></h2>
    <?php echo $deal['notes']->render(); ?>

    <?php echo $this->custom_fields->render(); ?>

    <h2><?php echo ucwords(CRMText::_('COBALT_EDIT_PEOPLE')); ?></h2>

    <div class="large_info">

        <table class="com_cobalt_table" id="people_list">
            <th></th>
            <th><?php echo CRMText::_('COBALT_PEOPLE_NAME'); ?></th>
            <th><?php echo CRMText::_('COBALT_PEOPLE_PHONE'); ?></th>
            <th><?php echo CRMText::_('COBALT_PEOPLE_OWNER'); ?></th>
            <th><?php echo CRMText::_('COBALT_PEOPLE_TYPE'); ?></th>
            <th><?php echo CRMText::_('COBALT_PEOPLE_CONTACT'); ?></th>
            <?php
                $c = count($deal['people']);
                for ($i=0; $i<$c; $i++) {

                    $person = $deal['people'][$i];
                    $k=$i%2;
                    echo '<tr class="cobalt_row_'.$k.'">';
                        if ( array_key_exists('avatar',$person) && $person['avatar'] != "" ) {
                            echo '<td><img src="'.JURI::base().'libraries/crm/media/avatars/'.$person['avatar'].'"/></td>';
                        } else {
                            echo '<td><img src="'.JURI::base().'libraries/crm/media/images/person.png'.'"/></td>';
                        }
                        echo '<td>'.$person['last_name'] . ', ' . $person['first_name'] . '</td>';
                        echo '<td>'.$person['phone'].'</td>';
                        echo '<td>'.$person['owner_first_name'].' '.$person['owner_last_name'].'</td>';
                        echo '<td>'.ucwords($person['type']).'</td>';
                        echo '<td>'.CobaltHelperDate::formatDate($person['modified']).'</td>';
                    echo '</tr>';
                }
            ?>
        </table>

    </div>

    <h2><?php echo CRMText::_('COBALT_EDIT_CONVERSATIONS'); ?></h2>

    <div id="conversation_entries">
    <?php

        $c = count($deal['conversations']);

            for ($i=0; $i<$c; $i++) {

                $convo = $deal['conversations'][$i];
                echo '<div class="conversation">';

                    echo '<div class="header"><b>'.CobaltHelperDate::formatDate($convo['created']).'</b></div>';
                    echo '<div class="convo_info"><b>'.CRMText::_('COBALT_USER').'</b> '.CRMText::_('COBALT_WROTE').':</div>';
                    echo '<div class="convo">'.$convo['conversation'].'</div>';

                echo '</div>';

            }

    ?>
</div>
</div>
