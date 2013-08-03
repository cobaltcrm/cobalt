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

$person = $this->info[0];
?>

<div class="rightColumn">
    <div class="infoContainer" id="details">
        <h2><?php echo ucwords(TextHelper::_('COBALT_CONTACT_INFO')); ?></h2>
        <div class="infoBlock">
            <div class="infoLabel">
                <?php
                if ( array_key_exists('avatar',$person) && $person['avatar'] != "" && $person['avatar'] != null ) {
                         echo '<td class="avatar" ><img id="avatar_img_'.$person['id'].'" data-item-type="people" data-item-id="'.$person['id'].'" class="avatar" src="'.JURI::base().'src/Cobalt/media/avatars/'.$person['avatar'].'"/></td>';
                    } else {
                        echo '<td class="avatar" ><img id="avatar_img_'.$person['id'].'" data-item-type="people" data-item-id="'.$person['id'].'" class="avatar" src="'.JURI::base().'src/Cobalt/media/images/person.png'.'"/></td>';
                    } ?>
            </div>
            <div class="infoDetails">
                <span class="largeDetails"><?php echo $person['first_name'] . ' ' . $person['last_name']; ?></span><br />
                <span class="smallDetails"><?php echo $person['owner_first_name'] . ' ' . $person['owner_last_name']; ?></span><br />
                <?php if (array_key_exists('company_id',$person)) { ?>
                    <?php echo $person['company_name']; ?>
                <?php } ?>
            </div>
        </div>
        <div class="infoBlock">
            <div class="infoLabel"><?php echo TextHelper::_('COBALT_WORK_PHONE_SHORT'); ?></div>
            <div class="infoDetails"><?php echo $person['phone']; ?></div>
        </div>

        <div class="infoBlock">
            <div class="infoLabel"><?php echo TextHelper::_('COBALT_EMAIL_SHORT'); ?></div>
            <div class="infoDetails">
                <?php if (array_key_exists('email',$person)) { ?>
                    <?php echo $person['email']; ?>
                <?php } ?>
            </div>
        </div>

        <div class="infoBlock">
            <div class="infoLabel">&nbsp;</div>
            <div class="infoDetails">
                <div class="twitter_dark"></div>
                <div class="facebook_dark"></div>
                <div class="linkedin_dark"></div>
                <div class="aim_dark"></div>
            </div>
        </div>
    </div>
    <?php if ( array_key_exists('event_dock',$this) ) { ?>
        <div class="infoContainer" id='event_dock'>
            <h2><?php echo ucwords(TextHelper::_('COBALT_TASKS_AND_EVENTS')); ?></h2>
            <?php echo $this->event_dock->render(); ?>
        </div>
    <?php } ?>

    <?php if ( array_key_exists('twitter_user',$person) ) { ?>
    <div class="infoContainer" id="twitter_dock">
        <h2><?php echo ucwords(TextHelper::_('COBALT_RECENT_TWEETS')); ?></h2>

    </div>
    <?php } ?>
</div>

<div class="leftColumn">

<h1><?php echo $person['first_name'].' '.$person['last_name']; ?></h1>

<div class="container">
    <div class="columncontainer">
        <div class="threecolumn">
            <div class="small_info first">
                <?php echo ucwords(TextHelper::_('COBALT_PERSON_TOTAL')); ?>
                <span class="amount"><?php echo ConfigHelper::getConfigValue('currency'); ?>0</span>
            </div>
            <div class="cobaltRow top">
                <div class="cobaltField"><?php echo ucwords(TextHelper::_('COBALT_COMPANY')); ?>:</div>
                <div class="cobaltValue"><?php echo $person['company_name']; ?></div>
            </div>
            <div class="cobaltRow">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_OWNER'); ?>:</div>
                <div class="cobaltValue"><?php echo $person['owner_first_name'].' '.$person['owner_last_name']; ?></div>
            </div>
        </div>

        <div class="threecolumn">
            <div class="small_info middle">
                <?php echo ucwords(TextHelper::_('COBALT_PERSON_DEALS')); ?>:
                <span class="amount"><?php echo ConfigHelper::getConfigValue('currency'); ?>0</span>
            </div>
            <div class="cobaltRow top">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_TITLE'); ?>:</div>
                <div class="cobaltValue">
                    <?php echo ucwords($person['position']); ?>
                </div>
            </div>
            <div class="cobaltRow">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_TYPE'); ?>:</div>
                <div class="cobaltValue">
                    <?php echo ucwords($person['type']); ?>
                </div>
            </div>
        </div>

        <div class="threecolumn">
            <div class="small_info last">
                <?php echo TextHelper::_('COBALT_PERSON_CONTACTED'); ?>:
                <?php
                    echo DateHelper::formatDate($person['modified']);
                ?>
            </div>
            <div class="cobaltRow top">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_STATUS'); ?>:</div>
                <div class="cobaltValue"><?php echo $person['status_name']; ?></div>
            </div>
            <div class="cobaltRow">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_SOURCE'); ?>:</div>
                <div class="cobaltValue"><?php echo $person['source_name']; ?></div>
            </div>
        </div>
    </div>

<?php if ( array_key_exists('deals',$person) && count($person['deals']) > 0 ) { ?>
<h2 class="dotted"><?php echo ucwords(TextHelper::_('COBALT_EDIT_DEALS')); ?></h2>

    <div class="large_info">
        <table class="com_cobalt_table" id="deal_list">
            <th><?php echo ucwords(TextHelper::_('COBALT_DEAL_NAME')); ?></th>
            <th><?php echo ucwords(TextHelper::_('COBALT_DEAL_OWNER')); ?></th>
            <th class="right"><?php echo ucwords(TextHelper::_('COBALT_DEAL_AMOUNT')); ?></th>
            <?php
                $n = count($person['deals']);
                for ($i=0; $i<$n; $i++) {
                    $deal = $person['deals'][$i];
                    $k = $i%2;
                    echo '<tr class="cobalt_row_'.$k.'">';
                        echo '<td><a href="'.JRoute::_('index.php?view=deals&layout=deal&id='.$deal['id']).'">'.$deal['name'].'</a></td>';
                        echo '<td>'.$deal['owner_first_name'].' '.$deal['owner_last_name'].'</td>';
                        echo '<td><span class="amount">$'.$deal['amount'].'</span></td>';
                    echo '</tr>';
                }
            ?>
        </table>
    </div>
<?php } ?>

<?php echo $this->custom_fields->render(); ?>

<h2 class="dotted"><?php echo TextHelper::_('COBALT_EDIT_NOTES'); ?></h2>

<?php echo $person['notes']->render(); ?>

</div>
