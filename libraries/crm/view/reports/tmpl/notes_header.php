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
$app = JFactory::getApplication();
$view = $app->input->get('view');
$layout = $app->input->get('layout');
?>
<table class="table table-striped table-hover">
        <thead>
            <tr>
                <?php if ($view != "print") { ?>
                <th class="checkbox_column"><input type="checkbox" onclick="selectAll(this);" /></th>
                <?php } ?>
                <th><div class="sort_order"><a class="comp.name" onclick="sortTable('comp.name',this)"><?php echo CRMText::_('COBALT_COMPANY_REPORT_NAME'); ?></a></div></th>
                <th><div class="sort_order"><a class="deal.name" onclick="sortTable('deal.name',this)"><?php echo CRMText::_('COBALT_DEAL_REPORT_NAME'); ?></a></div></th>
                <th><div class="sort_order"><a class="person.last_name" onclick="sortTable('person.last_name',this)"><?php echo CRMText::_('COBALT_PERSON_NAME'); ?></a></div></th>
                <th><div class="sort_order"><a class="user.last_name" onclick="sortTable('user.last_name',this)"><?php echo CRMText::_('COBALT_OWNER'); ?></a></div></th>
                <th><div class="sort_order"><a class="n.created" onclick="sortTable('n.created',this)"><?php echo CRMText::_('COBALT_WRITTEN_ON'); ?></a></div></th>
                <th><div class="sort_order"><a class="cat.name" onclick="sortTable('cat.name',this)"><?php echo CRMText::_('COBALT_CATEGORY'); ?></a></div></th>
                <th><?php echo CRMText::_('COBALT_DESCRIPTION'); ?></th>
            </tr>
            <?php if ($view != "print") { ?>
            <tr>
                <?php
                    //get user state variables
                    $company_filter = $this->state->get('Note.'.$view.'.'.$layout.'.company_name');
                    $deal_filter = $this->state->get('Note.'.$view.'.'.$layout.'.deal_name');
                    $person_filter = $this->state->get('Note.'.$view.'.'.$layout.'.person_name');
                ?>
                <th></th>
                <th><input class="input input-small filter_input" name="company_name" type="text" value="<?php echo $company_filter; ?>"></th>
                <th><input class="input input-small filter_input" name="deal_name" type="text" value="<?php echo $deal_filter; ?>"></th>
                <th><input class="input input-small filter_input" name="person_name" type="text" value="<?php echo $person_filter; ?>"></th>
                <th>
                   <select class="span1 filter_input" name="owner_id" id="owner_id">
                        <?php $user_filter = $this->state->get('Note.reports.notes.owner_id'); ?>
                        <?php if ( CobaltHelperUsers::getRole() != 'basic' ) { ?>
                            <?php   $all = array();
                                $all[] = JHTML::_('select.option','all',CRMText::_('COBALT_ALL'));
                                echo JHtml::_('select.options',$all,'value','text',$user_filter,true);
                            ?>
                        <?php } ?>
                         <optgroup label="<?php echo CRMText::_('COBALT_MEMBERS'); ?>" class="member" id="member" >
                            <?php   $member = array();
                                    $member[] = JHTML::_('select.option',CobaltHelperUsers::getUserId(),CRMText::_('COBALT_ME'));
                                    echo JHtml::_('select.options',$member,'value','text',$user_filter,true);
                            ?>
                            <?php echo JHtml::_('select.options', $this->user_names, 'value', 'text', $user_filter, true); ?>
                        </optgroup>
                        <?php if ( CobaltHelperUsers::getRole() == 'exec' ) { ?>
                        <optgroup label="<?php echo CRMText::_('COBALT_TEAM'); ?>" class="team" id="team" >
                            <?php echo JHtml::_('select.options', $this->team_names, 'value', 'text', $user_filter, true); ?>
                        </optgroup>
                        <?php } ?>
                    </select>
                </th>
                <th>
                    <select class="span1 filter_input" name="created">
                        <?php $created_filter = $this->state->get('Note.'.$view.'.'.$layout.'.created'); ?>
                        <option value=""><?php echo CRMText::_('COBALT_ALL'); ?></option>
                        <?php echo JHtml::_('select.options', $this->created_dates, 'value', 'text', $created_filter, true); ?>
                    </select>
                </th>
                <th>
                    <select class="span1 filter_input" name="category_id">
                        <?php $category_filter = $this->state->get('Note.'.$view.'.'.$layout.'.category_id'); ?>
                        <option value=""><?php echo CRMText::_('COBALT_ALL'); ?></option>
                        <?php echo JHtml::_('select.options',$this->categories, 'id', 'name', $category_filter, true); ?>
                    </select>
                </th>
                <th></th>
            </tr>
            <?php } ?>
        </thead>
        <tfoot>
            <tr>
                <?php if ($view != "print") { ?>
                <td></td>
                <?php } ?>
                <td><?php echo CRMText::_('COBALT_TOTAL_NOTES_FOUND').'<span class="count">'.count($this->note_entries)."</span>"; ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
        <tbody class="results" id="reports">
