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
<form id="note_edit" method="post" action="<?php echo 'index.php?controller=save&model=company&return=companies'; ?>" onsubmit="return save(this)" >
    <div id="editForm">
        <div class="cobaltRow">
            <div class="cobaltField"><?php echo TextHelper::_('COBALT_CATEGORY'); ?></div>
            <div class="cobaltValue">
                <select data-native-menu="false" data-overlay-theme="a" data-theme="c" name="category_id" tabindex="-1">
                    <?php
                        $categories = EventHelper::getCategories();
                        echo JHtml::_('select.options', $categories, 'value', 'text', "", true);
                    ?>
                </select>
            </div>
        </div>
        <div class="cobaltRow">
            <div class="cobaltField"><?php echo TextHelper::_('COBALT_CONTENT'); ?></div>
            <div class="cobaltValue">
                <textarea class="form-control" name="note"></textarea>
            </div>
        </div>
        <input data-theme="c" type="button" name="submit" onclick="addNoteEntry('note_edit');"  value="<?php echo TextHelper::_('COBALT_SUBMIT'); ?>" />

    </div>
</form>
