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

<div class="container-fluid">

    <?php echo $this->menu['quick_menu']->render(); ?>
    <div class="row">
        <div class="col-sm-12" id="content">
            <div class="row">
                <?php echo $this->menu['menu']->render(); ?>
                <div class="col-md-9">

                    <form action="<?php echo RouteHelper::_('index.php'); ?>" data-ajax="1" method="post" name="adminForm" id="adminForm" class="form-horizontal" >
                        <legend>
                            <div class="col-sm-9">
                                <h2><?php echo TextHelper::_("COBALT_EDITING_CUSTOM_FIELD"); ?></h2>
                            </div>
                            <div class="col-sm-3">
                                <?php echo $this->toolbar->render(); ?>
                            </div>
                            <div class="clearfix"></div>
                        </legend>

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="name">
                                <?php echo JText::_('COBALT_NAME'); ?>
                            </label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control required" name="name" id="name" value="<?php echo $this->custom->name; ?>" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="select-custom-type">
                                Type
                            </label>
                            <div class="col-sm-10">
                                <select class="form-control required" id="select-custom-type" name="type">
                                    <option value="">- Select Custom Field Type -</option>
                                    <?php echo JHtml::_('select.options', $this->custom_types, 'value', 'text', $this->custom->type, true);?>
                                </select>
                            </div>
                        </div>

                        <legend>Field Information</legend>

                        <div id="custom_field_data"></div>
                        <div>
                            <?php if ($this->custom->id) { ?>
                                <input type="hidden" name="id" value="<?php echo $this->custom->id; ?>" />
                            <?php } ?>
                            <input type="hidden" name="task" value="save" />
                            <input type="hidden" name="model" value="companycustom" />
                            <?php echo JHtml::_('form.token'); ?>
                        </div>

                    </form>

                    <div class="hide" id="custom_field_templates">
                        <div id="custom_field_number">
                            <ul>
                                <li>Numeric Fields can be used to capture items such as product units or other numeric values related to your companies.</li>
                            </ul>
                            <table>
                                <tr>
                                    <td><input type="checkbox" name="required" <?php if ( $this->custom->required) echo 'checked'; ?> /></td>
                                    <td>Make this field a required entry.</td>
                                </tr>
                            </table>
                        </div>
                        <div id="custom_field_text">
                            <ul>
                                <li>Text Fields are used to capture free form text. Examples: Billing Street Address, Email Address.</li>
                            </ul>
                            <table>
                                <tr>
                                    <td><input type="checkbox" name="required" <?php if ( $this->custom->required) echo 'checked'; ?> /></td>
                                    <td>Make this field a required entry.</td>
                                </tr>
                            </table>
                        </div>
                        <div id="custom_field_currency">
                            <ul>
                                <li>Currency Fields are used to capture items that have a $ monetary value. Examples: Service Revenue $, Product Sales $</li>
                            </ul>
                            <table>
                                <tr>
                                    <td><input type="checkbox" name="required" <?php if ( $this->custom->required) echo 'checked'; ?> /></td>
                                    <td>Make this field a required entry.</td>
                                </tr>
                            </table>
                        </div>
                        <div id="custom_field_picklist">
                            <ul>
                                <li>Picklist fields allow you to specify a list of pre-defined values for a user to pick from. Examples: Industry, Competitor, Regions, Product or Service Interest.</li>
                            </ul>
                            <div id="choices">
                                <?php if (isset($this->custom->values) && $this->custom->values != null) {
                                $values = $this->custom->values;
                                if ( count($values) > 0 ) {
                                    foreach ($values as $value) { ?>
                                        <div class="choices">
                                            <table>
                                                <tr>
                                                    <td>Enter Choice</td>
                                                    <td><input class="form-control required" type="text" name="values[]" value="<?php echo $value; ?>" /></td>
                                                    <td><a class="btn btn-danger remove_values">Remove</a></td>
                                                </tr>
                                            </table>
                                        </div>
                                    <?php }
                                }} else { ?>
                                     <div class="choices">
                                        <table>
                                            <tr>
                                                <td>Enter Choice</td>
                                                <td><input class="form-control required" type="text" name="values[]" value="" /></td>
                                                <td><a class="btn btn-danger remove_values">Remove</a></td>
                                            </tr>
                                        </table>
                                    </div>
                                <?php } ?>
                            </div>
                            <table>
                                <tr>
                                    <td><a class="btn btn-primary" id="add_values">Add More Choices</a></td>
                                </tr>
                           </table>
                           <table>
                                <tr>
                                    <td><input type="checkbox" name="multiple_selections" <?php if ( $this->custom->multiple_selections) echo 'checked'; ?> /></td>
                                    <td>Users can select more than one value</td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox" name="required" <?php if ( $this->custom->required) echo 'checked'; ?> /></td>
                                    <td>Make this field a required entry.</td>
                                </tr>
                            </table>
                        </div>
                        <div id="choice_template">
                            <div class="choices">
                                <table>
                                    <tr>
                                        <td>Enter Choice</td>
                                        <td><input class="form-control required" type="text" name="values[]" value="" /></td>
                                        <td><a class="btn btn-danger remove_values">Remove</a></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                       <div id="custom_field_date">
                           <ul>
                               <li>Date fields allow you to capture important dates related to your companies.</li>
                           </ul>
                           <table>
                                <tr>
                                    <td><input type="checkbox" name="required" <?php if ( $this->custom->required) echo 'checked'; ?> /></td>
                                    <td>Make this field a required entry.</td>
                                </tr>
                            </table>
                       </div>
                    </div>
                </div>
            </div>
        </div>
        <?php echo $this->menu['quick_menu']->render(); ?>
    </div>
</div>
