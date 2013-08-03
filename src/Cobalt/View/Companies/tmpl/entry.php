<?php
    $company = $this->company;
    $k = isset($this->k) ? $this->k : 0;
?>
<tr id='list_row_<?php echo $company['id']; ?>' class='cobalt_row_<?php echo $k; ?>'>
    <td><input type="checkbox" name="ids[]" value="<?php echo $company['id']; ?>" /></td>
    <?php if ( array_key_exists('avatar',$company) && $company['avatar'] != "" && $company['avatar'] != null ) { ?>
        <td class="avatar" ><img id="avatar_img_<?php echo $company['id']; ?>" data-item-type="companies" data-item-id="<?php echo $company['id']; ?>" class="avatar" src="<?php echo JURI::base().'src/Cobalt/media/avatars/'.$company['avatar']; ?>" /></td>
    <?php } else { ?>
        <td class="avatar" ><img id="avatar_img_<?php echo $company['id']; ?>" data-item-type="companies" data-item-id="<?php echo $company['id']; ?>" class="avatar" src="<?php echo JURI::base().'src/Cobalt/media/images/company.png'; ?>"/></td>
    <?php } ?>
    <td class="list_edit_button" id="list_<?php echo $company['id']; ?>" >
        <div class="title_holder">
            <a href="<?php echo JRoute::_('index.php?view=companies&layout=company&company_id='.$company['id']); ?>"><?php echo $company['name']; ?></a>
        </div>
        <address><?php echo $company['address_formatted']; ?></address>
        <div class="hidden"><small><?php echo $company['description']; ?></small></div>
    </td>
    <td class="contact" ><?php echo $company['phone'].'<br>'.$company['email']; ?></td>
    <td class="added" ><?php echo DateHelper::formatDate($company['created']); ?></td>
    <td class="updated" ><?php echo DateHelper::formatDate($company['modified']); ?></td>
    <td class="notes" >
        <div class="btn-group">
            <a rel="tooltip" title="<?php echo TextHelper::_('COBALT_VIEW_CONTACTS'); ?>" data-placement="bottom" class="btn" href="javascript:void(0);" onclick="showCompanyContactsDialogModal(<?php echo $company['id']; ?>);"><i class="icon-user"></i></a>
            <a rel="tooltip" title="<?php echo TextHelper::_('COBALT_VIEW_NOTES'); ?>" data-placement="bottom" class="btn" href="javascript:void(0);" onclick="openNoteModal(<?php echo $company['id']; ?>,'company');"><i class="icon-file"></i></a>
        </div>
    </td>
</tr>
