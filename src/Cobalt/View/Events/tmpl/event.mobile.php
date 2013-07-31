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
defined( '_CEXEC' ) or die( 'Restricted access' ); ?>

<script type="text/javascript">
    var loc = "event";
</script>
<?php $event = $this->events; ?>

    <div data-role='header' data-theme='b'>
        <h1><?php echo $event['name']; ?></h1>
            <a href="<?php echo JRoute::_('index.php?view=dashboard'); ?>" data-icon="back" class="ui-btn-left">
                <?php echo TextHelper::_('COBALT_BACK'); ?>
            </a>
    </div>

        <div data-role="collapsible">
            <h3><?php echo TextHelper::_('COBALT_DETAILS'); ?></h3>
            <?php if ( array_key_exists('description',$event) && $event['description'] != "" ) { ?>
            <div data-role="content">
                <?php echo $event['description']; ?>
            </div>
            <?php } ?>
            <div data-role="fieldcontain">
                 <fieldset data-role="controlgroup">
                    <legend class="ui-hidden-accessible"></legend>
                    <?php $event_status = $event['completed'] == 1 ? TextHelper::_('COBALT_MARK_INCOMPLETE') : TextHelper::_('COBALT_MARK_COMPLETE'); ?>
                    <input <?php if ( $event['completed'] == 1 ) echo 'checked="checked"'; ?> data-theme="c" onclick="markEventComplete(<?php echo $event['id']; ?>);" type="checkbox" name="completed" id="completed" class="custom" />
                    <label id="completed_label" for="completed"><?php echo $event_status; ?></label>
                </fieldset>
            </div>
        </div>

        <?php if ( count($this->people) > 0 ){ foreach ($this->people  as $person) { ?>
            <div data-role="collapsible">
                <h3><?php echo $person['first_name'].' '.$person['last_name']; ?></h3>
                    <ul data-inset='true' data-role='listview' data-theme="c">
                    <?php if ( array_key_exists('phone',$person) && $person['phone']!="") { ?>
                    <li>
                        <a href="tel://<?php echo $person['phone']; ?>" rel="external"><?php echo $person['phone']; ?></a>
                    </li>
                    <?php } ?>
                    <?php if ( array_key_exists('email',$person) && $person['email']!="") { ?>
                    <li>
                        <a href="mailto:<?php echo $person['email']; ?>" rel="external"><?php echo $person['email']; ?></a>
                    </li>
                    <?php } ?>
                    <?php if ( array_key_exists('home_address_1',$person) && $person['home_address_1']!="") { ?>
                    <li>
                        <a href="<?php echo JRoute::_('index.php?view=people&layout=directions&id='.$person['id']); ?>">
                            <?php if ( array_key_exists('home_address_1',$person) ) echo $person['home_address_1'].' '.$person['home_city'].', '.$person['home_state'].' '.$person['home_zip']; ?>
                        </a>
                    </li>
                    <?php } ?>
                </ul>
            </div>
        <?php } }?>

            <?php if ($event['company_name'] !="") { ?>
            <div data-role="collapsible">
                <h3><?php echo $event['company_name']; ?></h3>
                    <ul data-inset='true' data-role='listview' data-theme="c">
                    <?php if ( array_key_exists('company_phone',$event) && $event['company_phone']!="") { ?>
                    <li>
                        <a href="tel://<?php echo $event['company_phone']; ?>" rel="external"><?php echo $event['company_phone']; ?></a>
                    </li>
                    <?php } ?>
                    <?php if ( array_key_exists('company_website',$event) && $event['company_website']!="") { ?>
                    <li>
                        <a href="<?php echo $event['company_website']; ?>" rel="external"><?php echo $event['company_website']; ?></a>
                    </li>
                    <?php } ?>
                    <?php if ( array_key_exists('address_1',$event) && $event['company_address_1']!="") { ?>
                    <li>
                        <a href="<?php echo JRoute::_('index.php?view=companies&layout=directions&id='.$event['company_id']); ?>">
                            <?php if ( array_key_exists('company_address_1',$event) ) echo $event['company_address_1'].' '.$event['company_home_city'].', '.$event['company_home_state'].' '.$event['company_home_zip']; ?>
                        </a>
                    </li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>

        <?php if ($event['deal_name']!="") { ?>
            <div data-role="collapsible">
                <h3><?php echo ucwords(TextHelper::_('COBALT_ASSOCIATED_DEALS')); ?></h3>
                <ul data-inset='true' data-role='listview' data-theme="c">
                    <li>
                        <a href="<?php echo JRoute::_('index.php?view=deals&layout=deal&id='.$event['association_id']); ?>">
                            <h3><?php echo $event['deal_name']; ?></h3>
                            <div class="ui-li-count"><?php echo ConfigHelper::getCurrency().$event['deal_amount']; ?></div>
                        </a>
                    </li>
                </ul>
            </div>
        <?php }
