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

//define person
$person = $this->people[0];

?>

<script type="text/javascript">
    var id  = <?php echo $person['id']; ?>;
    var loc = 'person';
    var model = 'people';
    var person_id  = <?php echo $person['id']; ?>;
    var association_type = 'person';
</script>

<div data-role='header' data-theme='b'>
    <h1><?php echo $person['first_name'].' '.$person['last_name']; ?></h1>
        <a href="<?php echo JRoute::_('index.php?view=dashboard'); ?>" data-icon="back" class="ui-btn-left">
        <?php echo CRMText::_('COBALT_BACK'); ?>
    </a>
</div>

<div data-role='content' data-theme='b'>
    <p><?php if ( array_key_exists('position',$person) ) { echo $person['position'] ? $person['position'].', '.CRMText::_('COBALT_AT'): '' ; } ?>
        <strong><?php if ( array_key_exists('company_name',$person ) ) echo $person['company_name']; ?></strong>
    </p>
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
        <?php if ( array_key_exists('address_1',$person) && $person['address_1']!="") { ?>
        <li>
            <a href="<?php echo JRoute::_('index.php?view=people&layout=directions&id='.$person['id']); ?>">
                <?php if ( array_key_exists('home_address_1',$person) ) echo $person['home_address_1'].' '.$person['home_city'].', '.$person['home_state'].' '.$person['home_zip']; ?>
            </a>
        </li>
        <?php } ?>
    </ul>

    <div data-role="collapsible" data-collapsed="false">
        <h3><?php echo CRMText::_('COBALT_EDIT_NOTES'); ?></h3>
            <ul data-inset='true' data-role='listview' data-theme="c"  id="notes">
                <?php echo $person['notes']->render(); ?>
            </ul>
    </div>

    <div data-role="collapsible">
            <h3><?php echo CRMText::_('COBALT_TASKS_AND_EVENTS'); ?></h3>
            <ul data-inset='true' data-role='listview' data-theme="c">
                <?php echo $this->event_dock->render(); ?>
            </ul>
    </div>
<?php // TODO: Format currency to local ?>
    <div data-role="collapsible">
            <h3><?php echo CRMText::_('COBALT_EDIT_DEALS'); ?></h3>
            <ul data-inset='true' data-role='listview' data-theme="c">
                <?php
                $n = count($person['deals']);
                for ($i=0; $i<$n; $i++) {
                    $deal = $person['deals'][$i];
                    $k = $i%2;

                    echo '<li><a href="'.JRoute::_('index.php?view=deals&layout=deal&id='.$deal['id']).'">';
                    echo '<span class="ui-li-count">'.$deal['amount'].'</span>';
                    echo $deal['name'];
                    echo '</a></li>';
                }
                ?>
            </ul>
    </div>

    <div data-role="collapsible">
        <h3><?php echo CRMText::_('COBALT_ADD_NOTE'); ?></h3>
        <?php echo $this->add_note->render(); ?>
    </div>
        <div data-role="collapsible" data-collapsed="false">
        <h3><?php echo CRMText::_('COBALT_CREATE_TASK'); ?></h3>
            <?php echo $this->add_task->render(); ?>
        </div>
</div>
