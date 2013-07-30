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
defined( '_JEXEC' ) or die( 'Restricted access' );

//define deal
$deal = $this->dealList[0];?>
<script type="text/javascript">
    var id = <?php echo $deal['id']; ?>;
    var deal_id = <?php echo $deal['id']; ?>;
    var loc = "deal";
    var COBALT_CHANGE_BUTTON = "<?php echo CRMText::_('COBALT_CHANGE_BUTTON'); ?>";
    var AMOUNT = <?php $deal['amount'] = ( $deal['amount'] == 0 ) ? 0 : $deal['amount']; echo $deal['amount']; ?>;
    var archived = <?php echo $deal['archived']; ?>;
    var association_type = 'deal';
</script>

    <div data-role='header' data-theme='b'>
        <h1><?php echo $deal['name']; ?></h1>
            <a href="<?php echo JRoute::_('index.php?view=dashboard'); ?>" data-icon="back" class="ui-btn-left">
            <?php echo CRMText::_('COBALT_BACK'); ?>
        </a>
    </div>

<div data-role='content' data-theme='b'>
    <h2><?php echo $deal['summary'] ; ?></h2>

    <div data-role="collapsible" data-collapsed="false">
        <h3><?php echo ucwords(CRMText::_('COBALT_DEAL_DETAILS')); ?></h3>
        <div class="ui-grid-a ui-bar ui-bar-c">
            <div class="ui-block-a"><?php echo CRMText::_('COBALT_EDIT_AMOUNT'); ?>: </div>
            <div class="ui-block-b">$<?php echo $deal['amount']; ?></div>

            <div class="ui-block-a"><?php echo CRMText::_('COBALT_EDIT_COMPANY'); ?></div>
            <div class="ui-block-b"><a href="<?php echo JRoute::_('index.php?view=companies&layout=company&id='.$deal['company_id']); ?>"><?php echo ucwords($deal['company_name']); ?></a></div>

            <div class="ui-block-a"><?php echo CRMText::_('COBALT_EDIT_OWNER'); ?></div>
            <div class="ui-block-b"><?php echo $deal['owner_first_name'].' '.$deal['owner_last_name']; ?></div>
            <div class="ui-block-a"><?php echo CRMText::_('COBALT_EDIT_AGE'); ?>: </div>
            <div class="ui-block-b">
                    <?php
                        $created 	= strtotime($deal['created']);
                        $current 	= time();
                        $diff		= $current - $created;
                        $days		= intval(floor($diff/86400));
                        echo $days . ' ' . CRMText::_('COBALT_DAYS');
                    ?>
            </div>
            <div class="ui-block-a"><?php echo CRMText::_('COBALT_EDIT_STAGE'); ?></div>
            <div class="ui-block-b"><?php echo $deal['stage_name']; ?></div>
            <div class="ui-block-a"><?php echo CRMText::_('COBALT_EDIT_PROBABILITY'); ?></div>
            <div class="ui-block-b"><?php echo $deal['probability']; ?>%</div>
            <div class="ui-block-a"><?php echo CRMText::_('COBALT_EXP_CLOSE'); ?>: </div>
            <div class="ui-block-b"><?php echo CobaltHelperDate::formatDate($deal['expected_close']); ?></div>
            <div class="ui-block-a"><?php echo CRMText::_('COBALT_EDIT_STATUS'); ?></div>
            <div class="ui-block-b"><?php echo $deal['status_name']; ?></div>
            <div class="ui-block-a"><?php echo CRMText::_('COBALT_EDIT_SOURCE'); ?></div>
            <div class="ui-block-b"><?php echo $deal['source_name']; ?></div>
        </div>
    </div>

    <div data-role="collapsible">
            <h3><?php echo ucwords(CRMText::_('COBALT_CONTACT_INFO')); ?></h3>

        <ul data-inset='true' data-role='listview' data-theme="c">
            <?php if ( array_key_exists('people',$deal) && count($deal['people']) > 0 ){ foreach ($deal['people'] as $person) { ?>
                <li>
                    <a href="<?php echo JRoute::_('index.php?view=person&id='.$person['id']); ?>">
                        <img src="<?php echo JURI::base().'libraries/crm/media/images/person.png'; ?>" class="ui-li-thumb">
                        <h3 class="ui-li-heading"><?php echo $person['first_name'].' '.$person['last_name']; ?></h3>
                        <p class="ui-li-desc"><?php echo ucwords(JText::sprintf('COBALT_MOBILE_PERSON_DESC',$person['position'],$person['company_name'])); ?></p>
                    </a>
                </li>
    <?php } } ?>
        </ul>
    </div>

    <div data-role="collapsible">
            <h3><?php echo ucwords(CRMText::_('COBALT_TASKS_AND_EVENTS')); ?></h3>
            <ul data-inset='true' data-role='listview' data-theme="c" id="events">
                <?php echo $this->event_dock->render(); ?>
            </ul>
    </div>

    <div data-role="collapsible">
        <h3><?php echo CRMText::_('COBALT_EDIT_NOTES'); ?></h3>
            <ul data-inset='true' data-role='listview' data-theme="c" id="notes">
                <?php echo $deal['notes']->render(); ?>
            </ul>
    </div>

    <div data-role="collapsible">
        <h3><?php echo CRMText::_('COBALT_ADD_NOTE'); ?></h3>
        <?php echo $this->add_note->render(); ?>
    </div>

    <div data-role="collapsible" data-collapsed="false">
    <h3><?php echo ucwords(CRMText::_('COBALT_CREATE_TASK')); ?></h3>
        <?php echo $this->add_task->render(); ?>
    </div>

</div>
