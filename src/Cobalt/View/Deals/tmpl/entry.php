<?php
$deal = $this->deal;
$deal->status_name = $deal->status_name == "" ? "none" : $deal->status_name;
$stage_color = TextHelper::_('COBALT_DEFAULT_DEAL_STAGE_COLOR');
if (count($this->stages) > 0) {
    foreach ($this->stages as $stage) {
        if (isset($stage['id']) && $stage['id'] == $deal->stage_id) {
            $stage_color =  $stage['color'];
        }
    }
}

$stage_name = ( array_key_exists('stage_id',$deal) && $deal->stage_id != 0 ) ? $deal->stage_name : TextHelper::_('COBALT_CLICK_TO_EDIT');
$source_name = ( array_key_exists('source_id',$deal) && $deal->source_id != 0 ) ? $deal->source_name : TextHelper::_('COBALT_CLICK_TO_EDIT');
$expected_close = $deal->expected_close != "0000-00-00 00:00:00" ? DateHelper::formatDate($deal->expected_close) : TextHelper::_('COBALT_NOT_SET');
$actual_close = $deal->actual_close != "0000-00-00 00:00:00" && $deal->closed != 0 ? DateHelper::formatDate($deal->actual_close) : $actual_close = TextHelper::_('COBALT_ACTIVE_DEAL');
$extras = '<b>'.TextHelper::_('COBALT_PRIMARY_CONTACT').'</b>
            <a href="'.RouteHelper::_('index.php?view=people&layout=person&id='.$deal->primary_contact_id).'">'.$deal->primary_contact_first_name.'</a><br>
            <b>'.TextHelper::_('COBALT_NEXT_ACTION').'</b><br>';
?>
<tr id="list_row_<?php echo $deal->id; ?>" >
    <td><input type="checkbox" class="export" name="ids[]" value="<?php echo $deal->id; ?>" /></td>
    <td id="list_<?php echo $deal->id; ?>">
        <div class="title_holder">
            <a href="<?php echo RouteHelper::_('index.php?view=deals&layout=deal&id='.$deal->id); ?>"><?php echo $deal->name; ?></a>
        </div>
    </td>
    <td class="company">
        <a href="<?php echo RouteHelper::_('index.php?view=companies&layout=company&id='.$deal->company_id); ?>"><?php echo $deal->company_name; ?></a><br>
    </td>
    <td class="amount"><?php echo ConfigHelper::getCurrency().$deal->amount; ?></td>
    <td class='status' >
        <div class='dropdown'>
            <a href='javascript:void(0);' class='dropdown-toggle update-toggle-html' role='button' data-toggle='dropdown' id="deal_status_<?php echo $deal->id; ?>_link">
                <span class="deal-status-<?php echo $deal->status_name; ?>"></span>
            </a>
            <ul class="dropdown-menu" aria-labelledby="deal_stage_<?php echo $deal->id; ?>" role="menu">
            <?php if (isset($this->statuses) && count($this->statuses)) { foreach ($this->statuses as $id => $class) { ?>
                <li>
                    <a href="javascript:void(0)" class="stage_select dropdown_item" data-field="status_id" data-item="deal" data-item-id="<?php echo $deal->id; ?>" data-value="<?php echo $id; ?>">
                        <span class="deal-status-<?php echo strtolower($class); ?>"></span>
                    </a>
                </li>
            <?php }} ?>
            </ul>
        </div>
    </td>
    <td class="stage">
        <div class='dropdown'>
            <a href='javascript:void(0);' class='dropdown-toggle update-toggle-html' role='button' data-toggle='dropdown' id='deal_stage_<?php echo $deal->id; ?>_link'>
                <?php echo $stage_name; ?>
                <div class="status-dot" style="background-color: #<?php echo $stage_color; ?>"></div>
            </a>
            <ul class="dropdown-menu" aria-labelledby="deal_stage_<?php echo $deal->id; ?>" role="menu">
            <?php if (isset($this->stages) && count($this->stages) ){ foreach ($this->stages as $id => $stage) { ?>
                <li>
                    <a href="javascript:void(0)" class="stage_select dropdown_item" data-field="stage_id" data-item="deal" data-item-id="<?php echo isset($deal->id) ? $deal->id : ''; ?>" data-value="<?php echo isset($stage['id']) ? $stage['id'] : ''; ?>"><?php echo $stage['name']; ?>
                    <div class="status-dot" style="background-color: #<?php echo $stage['color']; ?>"></div></a>
                </li>
            <?php }} ?>
            </ul>
        </div>
    </td>
    <td class="source">
        <div class='dropdown'><a href='javascript:void(0);' class='dropdown-toggle update-toggle-html' role='button' data-toggle='dropdown' id='deal_source_<?php echo $deal->id; ?>_link'><?php echo $source_name; ?></a>
            <ul class="dropdown-menu" aria-labelledby="deal_source_<?php echo $deal->id; ?>" role="menu">
            <?php if ( count($this->sources) ){ foreach ($this->sources as $id => $source) { ?>
                <li><a href="javascript:void(0)" class="source_select dropdown_item" data-field="source_id" data-item="deal" data-item-id="<?php echo $deal->id; ?>" data-value="<?php echo $id; ?>"><?php echo $source; ?></a></li>
            <?php }} ?>
            </ul>
        </div>
    </td>
    <td class="expected_close"><?php echo $expected_close; ?></td>
    <td class="actual_close"><?php echo $actual_close; ?></td>
    <td class="contacts_notes">
        <div class="btn-group">
            <a rel="tooltip" title="<?php echo TextHelper::_('COBALT_VIEW_CONTACTS'); ?>" data-placement="bottom" class="btn btn-xs btn-default" href="javascript:void(0);" onclick="showDealContactsDialogModal(<?php echo $deal->id; ?>);"><i class="glyphicon glyphicon-user"></i></a>
            <a rel="tooltip" title="<?php echo TextHelper::_('COBALT_VIEW_NOTES'); ?>" data-placement="bottom" class="btn btn-xs btn-default" href="javascript:void(0);" onclick="openNoteModal(<?php echo $deal->id; ?>,'deal');"><i class="glyphicon glyphicon-file"></i></a>
            <a rel="popover" title="<?php echo TextHelper::_('COBALT_VIEW_DETAILS'); ?>" data-placement="top" data-html="true" data-content='<?php echo $extras; ?>' class="btn-default btn-xs btn" href="javascript:void(0);"><i class="glyphicon glyphicon-info-sign"></i></a>
        </div>
    </td>
</tr>
