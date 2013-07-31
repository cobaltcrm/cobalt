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

if (!isset($this->association_id)) { ?>
    <div data-role='header' data-theme='b'>
        <h1><?php echo ucwords(TextHelper::_('COBALT_ADD_TASK')); ?></h1>
            <a href="<?php echo JRoute::_('index.php?view=dashboard'); ?>" data-icon="back" class="ui-btn-left">
                <?php echo TextHelper::_('COBALT_BACK'); ?>
            </a>
    </div>

    <div data-role="content">
<?php } ?>

        <form id="task_edit" method="post" action="<?php echo JRoute::_('index.php?controller=save&model=event&return=events'); ?>" >
        <div id="editForm">
            <div class="cobaltRow">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_NAME'); ?></div>
                <div class="cobaltValue">
                    <input type="text" class="inputbox" name="name" value="" />
                </div>
            </div>
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
                <div class="cobaltField"></div>
                <div class="cobaltValue">
                    <label for="due_date"><?php echo TextHelper::_('COBALT_DUE_DATE'); ?>:</label>
                    <input type="date" name="due_date_hidden" class="inputbox" id="due_date" value="" />
                    <input type="hidden" name="due_date" id="due_date_hidden" value="" />
                </div>
            </div>
            <input data-theme="c" type="button" onclick="addTaskEntry();" name="submit"  value="<?php echo TextHelper::_('COBALT_SUBMIT'); ?>" />
            <?php if (isset($this->association_id)) { ?>
                <input type="hidden" name="association_id" id="association_id" value="<?php echo $this->association_id; ?>" />
                <input type="hidden" name="association_type" id="association_type" value="<?php echo $this->association_type; ?>" />
            <?php } ?>
        </div>
        </form>
    </div>
