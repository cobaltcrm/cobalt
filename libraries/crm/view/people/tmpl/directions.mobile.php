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
$person = $this->people[0];

?>

<script type="text/javascript">
    var loc = "person";
    var id = <?php echo $person['id']; ?>;
    var person_id = <?php echo $person['id']; ?>;
    var association_type = 'person';
</script>

    <div data-role="header" data-theme="b">
        <h1><?php echo CRMText::_('COBALT_GET_DIRECTIONS'); ?></h1>
        <a href="#" data-icon="back" class="ui-btn-left">
        <?php echo CRMText::_('COBALT_BACK'); ?>
    </a>	</div>
    <div data-role="content">
            <div class="ui-bar-c ui-corner-all ui-shadow" style="height:300px;"  id="map_canvas" data-address="<?php echo $person['home_address_1'].' '.$person['home_city'].' '.$person['home_state'].' '.$person['home_zip']; ?>" style="width: 100%; height: 250px;"></div>
            <p>
                <label for="from"><?php echo CRMText::_('COBALT_FROM'); ?></label>
                <input type="text" value="" class="ui-bar-c" id="from">
            </p>
            <p>
                <label for="to"><?php echo CRMText::_('COBALT_TO'); ?></label>
                <input type="text" value="<?php echo $person['home_address_1'].' '.$person['home_city'].', '.$person['home_state'].' '.$person['home_zip']; ?>" class="ui-bar-c" id="to">
            </p>
            <a data-icon="search" data-role="button" href="#" id="submit"><?php echo CRMText::_('COBALT_GET_DIRECTIONS'); ?></a>
        <div style="display:none;" class="ui-listview ui-listview-inset ui-corner-all ui-shadow" id="results">
            <div class="ui-li ui-li-divider ui-btn ui-bar-b ui-corner-top ui-btn-up-undefined"><?php echo CRMText::_('COBALT_RESULTS'); ?></div>
            <div id="directions"></div>
            <div class="ui-li ui-li-divider ui-btn ui-bar-b ui-corner-bottom ui-btn-up-undefined"></div>
        </div>
    </div>
