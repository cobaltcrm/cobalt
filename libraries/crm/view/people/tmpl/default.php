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
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<script type="text/javascript">
    var loc = "people";
    order_url = "<?php echo 'index.php?view=people&layout=list&format=raw&tmpl=component'; ?>";
    order_dir = "<?php echo $this->state->get('People.filter_order_Dir'); ?>";
    order_col = "<?php echo $this->state->get('People.filter_order'); ?>";
</script>

 <div data-remote="index.php?view=people&layout=edit&format=raw&tmpl=component" class="modal hide fade" id="personModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel"><?php echo ucwords(CRMText::_('COBALT_ADD_PERSON')); ?></h3>
        </div>
        <div class="modal-body">
            <p></p>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo ucwords(CRMText::_('COBALT_CANCEL')); ?></button>
            <button onclick="saveItem('edit_form')" class="btn btn-primary"><?php echo ucwords(CRMText::_('COBALT_SAVE')); ?></button>
        </div>
    </div>

<div class="page-header">
    <div class="btn-group pull-right">
        <a rel="tooltip" title="<?php echo CRMText::_('COBALT_PEOPLE_ADD'); ?>" data-placement="bottom" class="btn btn-success" role="button" href="#personModal" data-toggle="modal"><i class="icon-plus icon-white"></i></a>
        <a rel="tooltip" title="<?php echo CRMText::_('COBALT_IMPORT_PEOPLE'); ?>" data-placement="bottom"  class="btn" href="<?php echo JRoute::_('index.php?view=import&import_type=companies'); ?>"><i class="icon-circle-arrow-up"></i></a>
        <?php if ( CobaltHelperUsers::canExport() ){ ?>    
            <a rel="tooltip" title="<?php echo CRMText::_('COBALT_EXPORT_PEOPLE'); ?>" data-placement="bottom" class="btn"href="javascript:void(0)" onclick="exportCsv()"><i class="icon-share"></i></a>
        <?php } ?>  
    </div>

    <h1><?php echo ucwords(CRMText::_('COBALT_PEOPLE_HEADER')); ?></h1>
</div>
<ul class="inline-list">
    <li><span><?php echo CRMText::_('COBALT_SHOW'); ?></span></li>
    <li class="dropdown">
        <a class="dropdown-toggle update-toggle-text" data-toggle="dropdown" role="button" id="people_type_link" href="javascript:void(0);"><span class="dropdown-label"><?php echo $this->people_type_name; ?><span></a>
        <ul class="dropdown-menu" role="menu" aria-labelledby="people_type_link">
            <?php foreach ( $this->people_types as $title => $text ){
            echo "<li><a href='javascript:void(0);' class='filter_".$title."' onclick=\"companyType('".$title."')\">".$text."</a></li>";
            }?>
        </ul>
    </li> 
    <li><span><?php echo CRMText::_('COBALT_OWNED_BY'); ?></span></li>
    <li class="dropdown">
        <a class="dropdown-toggle update-toggle-text" href="javascript:void(0);" data-toggle="dropdown" role="button" id="people_user_link"><span class="dropdown-label"><?php echo $this->user_name; ?></span></a>
        <ul class="dropdown-menu update-toggle-text" role="menu" aria-labelledby="people_user_link">
            <li><a href="javascript:void(0);" class="filter_user_<?php echo $this->user_id; ?>" onclick="peopleUser(<?php echo $this->user_id; ?>,0)"><span class="dropdown-label"><?php echo CRMText::_('COBALT_ME'); ?><span></a></li>
            <?php if ( $this->member_role != 'basic' ){ ?>
                 <li><a href="javascript:void(0);" class="filter_user_all" onclick="peopleUser('all',0)"><?php echo CRMText::_('COBALT_ALL_USERS'); ?></a></li>
            <?php } ?>
            <?php
                if ( $this->member_role == 'exec' ){
                    if ( count($this->teams) > 0 ){
                        foreach($this->teams as $team){
                             echo "<li><a href='javascript:void(0);' class='filter_team_".$team['team_id']."' onclick='peopleUser(0,".$team['team_id'].")'>".$team['team_name'].CRMText::_('COBALT_TEAM_APPEND')."</a></li>";
                         }
                    }
                }
                if ( count($this->users) > 0 ){
                    foreach($this->users as $user){
                        echo "<li><a href='javascript:void(0);' class='filter_user_".$user['id']."' onclick='peopleUser(".$user['id'].")'>".$user['first_name']."  ".$user['last_name']."</a></li>";
                    }
                }
            ?>
        </ul>
    </li>
    <li><span><?php echo CRMText::_('COBALT_WHO'); ?></span></li>
    <li class="dropdown">
        <a class="dropdown-toggle update-toggle-text" href="javascript:void(0);" data-toggle="dropdown" role="button" id="people_stages_link"><span class="dropdown-label"><?php echo $this->stages_name; ?></span></a>
        <ul class="dropdown-menu" role="menu" aria-labelledby="people_stages_link">
            <?php foreach ( $this->stages as $title => $text ){
                echo "<li><a href='javascript:void(0);' class='filter_".$title."' onclick=\"peopleUpdated('".$title."')\">".$text."</a></li>";
            }?>
        </ul>
    </li>
    <li><span><?php echo CRMText::_('COBALT_AND_WITH_STATUS'); ?></span></li>
    <li class="dropdown">
        <a class="update-toggle-text dropdown-toggle" href="javascript:void(0);" data-toggle="dropdown" role="button" id="people_status_link"><span class="dropdown-label"><?php echo $this->status_name; ?></span></a>
        <ul class="dropdown-menu" role="menu" aria-labelledby="people_status_link">
            <li><a class="filter_any" onclick="peopleStatus('any')"><?php echo CRMText::_('COBALT_ANY_STATUS'); ?></a></li>
            <?php
                foreach ( $this->status_list as $key => $status ){
                    echo "<li><a href='javascript:void(0);' class='filter_".$status['id']."' onclick='peopleStatus(".$status['id'].")'>".$status['name']."</a></li>";
                }
            ?>            
        </ul>
    </li>
    <li>
        <span><?php echo CRMText::_('COBALT_NAMED'); ?></span>
        <input class="inputbox filter_input" name="name" type="text" placeholder="<?php echo CRMText::_('COBALT_ANYTHING'); ?>" value="<?php echo $this->people_filter; ?>">
    </li>
    <li class="filter_sentence">
        <div class="ajax_loader"></div>
    </li>
</ul>
<small><span id="people_matched"></span> <?php echo CRMText::_('COBALT_PEOPLE_MATCHED'); ?> <?php echo CRMText::_('COBALT_THERE_ARE'); ?> <?php echo $this->totalPeople; ?> <?php echo CRMText::_('COBALT_PEOPLE_IN_ACCOUNT'); ?></small>

<?php echo CobaltHelperTemplate::getListEditActions(); ?>
<form method="post" id="list_form" action="<?php echo JRoute::_('index.php?view=people'); ?>">
<table class="table table-striped table-hover" id="people">
		  <?php echo $this->people_list->render(); ?>
</table>
<input type="hidden" name="list_type" value="people" />
</form>
<?php /*
<div id="templates" style="display:none;">
    <div id="edit_task" style="display:none;"></div>
    <div id="note_modal" style="display:none;"></div>
    <div id="edit_event" style="display:none;"></div>
    <div id="edit_button"><a class="edit_button_link" id="edit_button_link" href="javascript:void(0)"></a></div>
    <div id="edit_list_modal" style="display:none;" ></div>
</div>
*/ ?>