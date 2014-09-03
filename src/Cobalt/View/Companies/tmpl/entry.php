<?php
    $company = $this->company;
    $k = isset($this->k) ? $this->k : 0;
?>
<tr id='list_row_<?php echo $company['id']; ?>' class='cobalt_row_<?php echo $k; ?>'>
    <td><input type="checkbox" name="ids[]" value="<?php echo $company['id']; ?>" /></td>
    <td class="list_edit_button" id="list_<?php echo $company['id']; ?>" >
        <div class="title_holder">
            <a href="<?php echo RouteHelper::_('index.php?view=companies&layout=company&company_id='.$company['id']); ?>"><?php echo $company['name']; ?></a>
        </div>
        <address><?php echo $company['address_formatted']; ?></address>
        <div class="hidden"><small><?php echo $company['description']; ?></small></div>
    </td>
    <td class="contact" ><?php echo $company['phone'].'<br>'.$company['email']; ?></td>
    <td class="added" ><?php echo DateHelper::formatDate($company['created']); ?></td>
    <td class="updated" ><?php echo DateHelper::formatDate($company['modified']); ?></td>
    <td class="notes" >
        <div class="btn-group">
            <a rel="tooltip" title="<?php echo TextHelper::_('COBALT_VIEW_CONTACTS'); ?>" data-placement="bottom" class="btn" href="javascript:void(0);" onclick="showCompanyContactsDialogModal(<?php echo $company['id']; ?>);"><i class="glyphicon glyphicon-user"></i></a>
            <a rel="tooltip" title="<?php echo TextHelper::_('COBALT_VIEW_NOTES'); ?>" data-placement="bottom" class="btn" href="javascript:void(0);" onclick="openNoteModal(<?php echo $company['id']; ?>,'company');"><i class="glyphicon glyphicon-file"></i></a>
        </div>
    </td>
</tr>
