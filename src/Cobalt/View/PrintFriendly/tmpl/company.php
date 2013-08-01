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

//define deal
$company = $this->info[0];

?>

<div class="rightColumn">
    <div class="infoContainer" id="details">
        <h2><?php echo ucwords(TextHelper::_('COBALT_COMPANY_DETAILS')); ?></h2>
        <div class="infoBlock">
            <div class="infoLabel">
                <?php if ( array_key_exists('avatar',$company) && $company['avatar'] != "" && $company['avatar'] != null ) {
                         echo '<td class="avatar" ><img id="avatar_img_'.$company['id'].'" data-item-type="companies" data-item-id="'.$company['id'].'" class="avatar" src="'.JURI::base().'libraries/crm/media/avatars/'.$company['avatar'].'"/></td>';
                    } else {
                        echo '<td class="avatar" ><img id="avatar_img_'.$company['id'].'" data-item-type="companies" data-item-id="'.$company['id'].'" class="avatar" src="'.JURI::base().'libraries/crm/media/images/company.png'.'"/></td>';
                    } ?>
            </div>
            <div class="infoDetails">
            </div>
        </div>
    </div>
    <?php if ( array_key_exists('event_dock',$this) ) { ?>
        <div class="infoContainer" id='event_dock'>
            <h2><?php echo ucwords(TextHelper::_('COBALT_TASKS_AND_EVENTS')); ?></h2>
            <?php echo $this->event_dock->render(); ?>
        </div>
    <?php } ?>
</div>

<div class="leftColumn">

<h1><?php echo $company['name']; ?></h1>

<div class="container">
    <div class="columncontainer">
        <div class="threecolumn">
            <div class="small_info first">
                <?php echo ucwords(TextHelper::_('COBALT_COMPANY_TOTAL')); ?></td>
                <span class="amount"><?php echo ConfigHelper::getConfigValue('currency'); ?>0</span></td>
            </div>
        </div>
        <div class="threecolumn">
            <div class="small_info middle">
                <?php echo ucwords(TextHelper::_('COBALT_COMPANY_DEALS')); ?>
                <span class="amount"><?php echo ConfigHelper::getConfigValue('currency'); ?>0</span>
            </div>
        </div>

        <div class="threecolumn">
            <div class="small_info">
                <?php echo ucwords(TextHelper::_('COBALT_COMPANY_CONTACTED')); ?>
                <?php echo DateHelper::formatDate($company['modified']); ?>
            </div>
        </div>
    </div>
<h2><?php echo TextHelper::_('COBALT_EDIT_NOTES'); ?></h2>

<?php echo $company['notes']->render(); ?>

<?php echo $this->custom_fields->render(); ?>

<h2><?php echo ucwords(TextHelper::_('COBALT_EDIT_DEALS')); ?></h2>

    <div class="large_info">
        <table class="com_cobalt_table" id="deal_list">
            <th><?php echo TextHelper::_('COBALT_DEAL_NAME'); ?></th>
            <th><?php echo TextHelper::_('COBALT_DEAL_OWNER'); ?></th>
            <th><span class="amount"><?php echo TextHelper::_('COBALT_DEAL_AMOUNT'); ?></span></th>
            <?php
                $n = count($company['deals']);
                for ($i=0; $i<$n; $i++) {
                    $deal = $company['deals'][$i];
                    $k = $i%2;
                    echo '<tr class="cobalt_row_'.$k.'">';
                        echo '<td>'.$deal['name'].'</td>';
                        echo '<td>'.$deal['owner_first_name'].' '.$deal['owner_last_name'].'</td>';
                        echo '<td><span class="amount">$'.$deal['amount'].'</span></td>';
                    echo '</tr>';
                }
            ?>
        </table>
    </div>

<h2><?php echo ucwords(TextHelper::_('COBALT_EDIT_PEOPLE')); ?></h2>

    <div class="large_info">

        <table class="com_cobalt_table" id="people_list">
            <th></th>
            <th><?php echo TextHelper::_('COBALT_PEOPLE_NAME'); ?></th>
            <th><?php echo TextHelper::_('COBALT_PEOPLE_PHONE'); ?></th>
            <th><?php echo TextHelper::_('COBALT_PEOPLE_OWNER'); ?></th>
            <th><?php echo TextHelper::_('COBALT_PEOPLE_TYPE'); ?></th>
            <th><?php echo TextHelper::_('COBALT_PEOPLE_CONTACT'); ?></th>
            <?php
                $c = count($company['people']);
                for ($i=0; $i<$c; $i++) {

                    $person = $company['people'][$i];
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
                        echo '<td>'.DateHelper::formatDate($person['modified']).'</td>';
                    echo '</tr>';
                }
            ?>
        </table>
    </div>

</div>
</div>
</div>
