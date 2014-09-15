<?php
    $person = $this->person;
    $k = isset($this->k) ? $this->k : 0;
    //assign null data
    $person['company_name'] = ( $person['company_name'] == '' ) ? TextHelper::_('COBALT_NO_COMPANY') : $person['company_name'];
    $person['status_name'] = ( $person['status_name'] == '' ) ? TextHelper::_('COBALT_NO_STATUS') : $person['status_name'];
    $person['source_name'] = ( $person['source_name'] == '' ) ? TextHelper::_('COBALT_NO_SOURCE') : $person['source_name'];
    //assign tabs
    $exp = explode('|',$person['tags']);
    $c = count($exp);
    $person['tags'] = '';
    for ($a=0; $a<$c; $a++) {
        $person['tags'] .= $exp[$a] . " ";
    }
    echo "<tr id='list_row_".$person['id']."' class='cobalt_row_".$k."'>";
        echo '<td><input type="checkbox" name="ids[]" value="'.$person['id'].'" /></td>';
        if ( array_key_exists('avatar',$person) && $person['avatar'] != "" && $person['avatar'] != null ) {
             echo '<td class="avatar" ><img id="avatar_img_'.$person['id'].'" data-item-type="people" data-item-id="'.$person['id'].'" class="avatar" src="'.JURI::base().'src/Cobalt/media/avatars/'.$person['avatar'].'"/></td>';
        } else {
            echo '<td class="avatar" ><img id="avatar_img_'.$person['id'].'" data-item-type="people" data-item-id="'.$person['id'].'" class="avatar" src="'.JURI::base().'src/Cobalt/media/images/person.png'.'"/></td>';
        }
        echo '<td class="list_edit_button" id="list_'.$person['id'].'" ><div class="title_holder"><a href="'.RouteHelper::_('index.php?view=people&layout=person&id='.$person['id']).'">'.$person['first_name'].' '.$person['last_name']."</a></div></td>";
        echo "<td class='company' ><a href='".RouteHelper::_('index.php?view=companies&layout=company&id='.$person['company_id'])."'>".$person['company_name']."</a></td>";
        echo "<td class='owner' >";
        echo '<div class="filters" data-item="people" data-field="owner_id" data-item-id="'.$person['id'].'" id="person_owner_'.$person['id'].'">';
        ?>
            <div class='dropdown'>
            <a href='javascript:void(0);' class='dropdown-toggle update-toggle-html' role='button' data-toggle='dropdown' id="person_owner_<?php echo $person['id']; ?>_link">
                <?php echo $person['owner_first_name'].' '.$person['owner_last_name']; ?>
            </a>
            <?php
                    $me = array(array('label'=>TextHelper::_('COBALT_ME'),'value'=>UsersHelper::getLoggedInUser()->id));
                    $users = UsersHelper::getUsers(null,TRUE);
                    $users = array_merge($me,$users); ?>
                    <ul class="dropdown-menu" aria-labelledby="deal_stage_<?php echo $person['id']; ?>" role="menu">
                <?php
                    if (count($users)) { foreach ($users as $id => $user) { ?>
                    <li><a href="javascript:void(0)" class="owner_select dropdown_item" data-field="owner_id" data-item="people" data-item-id="<?php echo $person['id']; ?>" data-value="<?php echo $user['value']; ?>">
                            <?php echo $user['label']; ?>
                        </a>
                    </li>
                <?php }} ?>
                </ul>
            </div>
        </td>
        <?php
        echo '<td class="email">'.$person['email'].'</td>';
        echo '<td class="phone">'.$person['phone'].'</td>';
        echo "<td class='status' >";
        ?>
            <div class='dropdown'>
            <a href='javascript:void(0);' class='dropdown-toggle update-toggle-html' role='button' data-toggle='dropdown' id="person_status_<?php echo $person['id']; ?>_link">
                <div class='person-status-color' style='background-color:#".$person['status_color']."'></div><div class='person-status'><?php echo $person['status_name']; ?></div>
            </a>
                <ul class="dropdown-menu" aria-labelledby="person_status<?php echo $person['id']; ?>" role="menu">
                    <?php $statuses = PeopleHelper::getStatusList();
                        if (count($statuses)) { foreach ($statuses as $key => $status) { ?>
                            <li><a href="javascript:void(0)" class="person_status_select dropdown_item" data-field="status_id" data-item="people" data-item-id="<?php echo $person['id']; ?>" data-value="<?php echo $status['id']; ?>">
                                    <div class="person-status-color" style="background-color:#<?php echo $status['color']; ?>"></div><div class="person-status"><?php echo $status['name']; ?></div>
                                </a>
                            </li>
                <?php }} ?>
                </ul>
            </div>
        </td>
        <td class='source' >
            <div class='dropdown'>
            <a href='javascript:void(0);' class='dropdown-toggle update-toggle-html' role='button' data-toggle='dropdown' id="person_source_<?php echo $person['id']; ?>_link">
                <?php echo $person['source_name']; ?>
            </a>
                <ul class="dropdown-menu" aria-labelledby="person_status<?php echo $person['id']; ?>" role="menu">
                    <?php $sources = DealHelper::getSources();
                        if ( count($sources) ){ foreach ($sources as $id => $name) { ?>
                            <li><a href="javascript:void(0)" class="source_select dropdown_item" data-field="source_id" data-item="people" data-item-id="<?php echo $person['id']; ?>" data-value="<?php echo $id; ?>">
                                    <?php echo $name; ?>
                                </a>
                            </li>
                    <?php }} ?>
            </ul>
        </div></td>
        <?php
        echo "<td class='type' >";
        ?>
            <div class='dropdown'>
            <a href='javascript:void(0);' class='dropdown-toggle update-toggle-html' role='button' data-toggle='dropdown' id="peron_type_<?php echo $person['id']; ?>_link">
                <?php echo ucwords($person['type']); ?>
            </a>
                <ul class="dropdown-menu" aria-labelledby="person_type_<?php echo $person['id']; ?>" role="menu">
                     <?php $types = PeopleHelper::getPeopleTypes(FALSE);
                        if ( count($types) ){ foreach ($types as $id => $name) { ?>
                            <li><a href="javascript:void(0)" class="person_type_select dropdown_item" data-field="type" data-item="people" data-item-id="<?php echo $person['id']; ?>" data-value="<?php echo ucwords($id); ?>">
                                    <?php echo ucwords($name); ?>
                                </a>
                            </li>
                <?php }} ?>
                </ul>
            </div>
        </td>
        <td class="notes"><a rel="tooltip" title="<?php echo TextHelper::_('COBALT_VIEW_NOTES'); ?>" data-placement="bottom" class="btn" href="javascript:void(0);" onclick="openNoteModal(<?php echo $person['id']; ?>, 'people');"><i class="glyphicon glyphicon-file"></i></a>
        <?php
        echo '<td class="address">'.$person['work_city'].'<br>'.$person['work_state'].'<br>'.$person['work_zip'].'<br>'.$person['work_country'].'</td>';
        echo '<td class="added">'.DateHelper::formatDate($person['created']).'</td>';
        echo '<td class="updated">'.DateHelper::formatDate($person['modified']).'</td>';
    echo "</tr>";
