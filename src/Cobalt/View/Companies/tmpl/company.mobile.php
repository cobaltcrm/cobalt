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
$company = $this->companies[0];

?>

<script type="text/javascript">
    var loc = "company";
    var id = <?php echo $company['id']; ?>;
    var company_id = <?php echo $company['id']; ?>;
    var association_type = 'company';
</script>

<div data-role='header' data-theme='b'>
    <h1><?php echo $company['name']; ?></h1>
        <a href="<?php echo JRoute::_('index.php?view=companies'); ?>" data-icon="back" class="ui-btn-left">
        <?php echo TextHelper::_('COBALT_BACK'); ?>
    </a>
</div>

<div data-role='content' data-theme='b'>
    <ul data-inset='true' data-role='listview' data-theme="c">
        <?php if ($company['phone']!="") { ?>
            <li>
                <a href="tel://<?php echo $company['phone']; ?>" rel="external"><?php echo $company['phone']; ?></a>
            </li>
        <?php } ?>

        <?php if ($company['address_1']!="") { ?>
        <li>
            <a href="<?php echo JRoute::_('index.php?view=companies&layout=directions&id='.$company['id']); ?>"><?php echo $company['address_1'].' '.$company['address_city'].' '.$company['address_state'].' '.$company['address_zip']; ?></a>
        </li>
        <?php } ?>

        <?php if ($company['website']!="") { ?>
        <li>
            <a href="<?php echo $company['website']; ?>" target="_blank"><?php echo $company['website']; ?></a>
        </li>
        <?php } ?>
    </ul>

    <div data-role="collapsible" data-collapsed="false">
        <h3><?php echo TextHelper::_('COBALT_EDIT_NOTES'); ?></h3>
            <ul data-inset='true' data-role='listview' data-theme="c" id="notes">
                <?php echo $company['notes']->render(); ?>
            </ul>
    </div>

    <div data-role="collapsible">
        <h3><?php echo TextHelper::_('COBALT_ADD_NOTE'); ?></h3>
        <?php echo $this->add_note->render(); ?>
    </div>
</div>
