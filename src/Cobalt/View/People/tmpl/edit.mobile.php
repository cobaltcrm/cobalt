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
$app = \Cobalt\Factory::getApplication();
?>

<div data-role='header' data-theme='b'>
    <h1><?php echo TextHelper::_('COBALT_ADD_PERSON'); ?></h1>
        <a href="<?php echo RouteHelper::_('index.php?view=people&task=save'); ?>" data-icon="back" class="ui-btn-left">
            <?php echo TextHelper::_('COBALT_BACK'); ?>
        </a>
</div>

<div data-role="content">

    <form id="edit_form" method="post" action="<?php echo 'index.php?task=save&model=people&return=people'; ?>" onsubmit="return save(this)" >
            <div id="editForm">
            <div class="cobaltRow">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_PERSON_FIRST'); ?><span class="required">*</span></div>
                <div class="cobaltValue wide"><input class="required inputbox" type="text" name="first_name" value="" /></div>
            </div>
            <div class="cobaltRow">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_PERSON_LAST'); ?><span class="required">*</span></div>
                <div class="cobaltValue wide"><input class="required inputbox" type="text" name="last_name" value=""/></div>
            </div>
            <div class="cobaltRow">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_PERSON_COMPANY'); ?></div>
                <div class="cobaltValue">
                    <input class="form-control" type="text" name="company" value=""/>
                </div>
            </div>
            <div class="cobaltRow">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_PERSON_POSITION'); ?></div>
                <div class="cobaltValue"><input class="form-control" type="text" name="position" value=""/></div>
            </div>

            <div class="cobaltRow">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_PERSON_PHONE'); ?></div>
                <div class="cobaltValue"><input class="form-control ui-input-text ui-body-b ui-corner-all ui-shadow-inset" type="phone" name="phone" value=""/></div>
            </div>
            <div class="cobaltRow">
                    <div class="cobaltField"><?php echo TextHelper::_('COBALT_PERSON_EMAIL'); ?></div>
                    <div class="cobaltValue"><input class="form-control" type="email" name="email" value=""/></div>
            </div>
            <div class="cobaltRow">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_PERSON_SOURCE'); ?></div>
                <div class="cobaltValue">
                    <select data-native-menu="false" data-overlay-theme="a" data-theme="c" name="source_id" tabindex="-1">
                        <?php $options = DropdownHelper::generateDropdown('source','','',true);
                             if (count($options) > 0) { foreach ($options as $option) {
                                 echo "<option value='".$option['id']."''>".$option['name']."</option>";
                             } } ?>
                    </select>
                </div>
            </div>
        <?php
            if ( $app->input->get('lead') ) {
                echo '<input type="hidden" name="type" value="lead" />';
            }
        ?>
        <input type="submit" name="submit"  value="<?php echo TextHelper::_('COBALT_SUBMIT'); ?>" />
    </form>
</div>
