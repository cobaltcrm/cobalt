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

<div class="page-header">

    <div class="modal fade" id="dealModal" tabindex="-1" role="dialog" aria-labelledby="dealModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content"></div>
        </div>
    </div>

    <div class="pull-right btn-group">
        <a rel="tooltip" title="<?php echo TextHelper::_('COBALT_ADD_DEALS'); ?>" data-placement="bottom" class="btn btn-success" role="button" href="index.php?view=deals&layout=edit&format=raw&tmpl=component" data-target="#dealModal" data-toggle="modal"><i class="glyphicon glyphicon-plus icon-white"></i></a>
        <a rel="tooltip" title="<?php echo TextHelper::_('COBALT_IMPORT_DEALS'); ?>" data-placement="bottom"  class="btn btn-default" href="<?php echo RouteHelper::_('index.php?view=import&import_type=deals'); ?>"><i class="glyphicon glyphicon-circle-arrow-up"></i></a>
        <?php if ( UsersHelper::canExport() ) { ?>
        <a rel="tooltip" title="<?php echo TextHelper::_('COBALT_EXPORT_DEALS'); ?>" data-placement="bottom" class="btn btn-default" href="javascript:void(0)" onclick="exportCsv()"><i class="glyphicon glyphicon-share"></i></a>
        <?php } ?>
    </div>

    <h1><?php echo ucwords(TextHelper::_('COBALT_DEALS_HEADER')); ?></h1>
</div>
    <ul class="list-inline filter-sentence">
        <li><span><?php echo TextHelper::_('COBALT_SHOW'); ?></span></li>
        <li class="dropdown">
            <a class="dropdown-toggle update-toggle-text" href="#" data-toggle="dropdown" role="button" id="deal_type_link" ><span class="dropdown-label"><?php echo $this->deal_type_name; ?><span></a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="deal_type_link">
                <?php foreach ($this->deal_types as $title => $text) { ?>
                     <li><a href='javascript:void(0);' class='filter_<?php echo $title; ?>' onclick="dealType('<?php echo $title; ?>')"><?php echo $text; ?></a></li>
                <?php }?>
            </ul>
        </li>
        <li><span><?php echo TextHelper::_('COBALT_FOR'); ?></span></li>
        <li class='dropdown'>
            <a class="dropdown-toggle update-toggle-text" href="#" data-toggle="dropdown" role="button" id="deal_user_link"><span class="dropdown-label"><?php echo $this->user_name; ?><span></a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="deal_user_link">
                <li><a class="filter_user_<?php echo $this->user_id ?>" onclick="dealUser(<?php echo $this->user_id ?>,0)"><?php echo TextHelper::_('COBALT_ME'); ?></a></li>
                <?php  if ($this->member_role != 'basic') { ?>
                     <li><a href="javascript:void(0);" class="filter_user_all" onclick="dealUser('all',0)"><?php echo TextHelper::_('COBALT_ALL_USERS'); ?></a></li>
                <?php } ?>
                <?php
                   if ($this->member_role == 'exec') {
                        if ( count($this->teams) > 0 ) {
                            foreach ($this->teams as $team) { ?>
                                 <li><a href='javascript:void(0);' class='filter_team_<?php echo $team['team_id']; ?>' onclick='dealUser(0,"<?php echo $team['team_id']; ?>")'><?php echo $team['team_name'].TextHelper::_('COBALT_TEAM_APPEND'); ?></a></li>
                             <?php }
                        }
                    }
                    if ( count($this->users) > 0 ) {
                        foreach ($this->users as $user) { ?>
                            <li><a href='javascript:void(0);' class='filter_user_<?php echo $user['id']; ?>' onclick='dealUser("<?php echo $user['id']; ?>",0)'><?php echo $user['first_name']."  ".$user['last_name']; ?></a></li>
                        <?php }
                    }
                ?>
            </ul>
        </li>
        <li><span><?php echo TextHelper::_('COBALT_IN'); ?></span></li>
        <li class="dropdown">
            <a class="dropdown-toggle update-toggle-text" href="#" data-toggle="dropdown" role="button" id="deal_stages_link"><span class="dropdown-label"><?php echo $this->stage_name; ?><span></a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="deal_stages_link">
                <?php foreach ($this->stages as $title => $text) { ?>
                   <li><a href='javascript:void(0);' class='filter_<?php echo $title; ?>' onclick="dealStage('<?php echo $title; ?>')"><?php echo $text; ?></a></li>
                <?php }?>
            </ul>
        </li>
        <li><span><?php echo TextHelper::_('COBALT_CLOSING'); ?></span></li>
        <li class="dropdown">
            <a class="dropdown-toggle update-toggle-text" href="#" data-toggle="dropdown" role="button" id="deal_closing_link"><span class="dropdown-label"><?php echo $this->closing_name; ?><span></a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="deal_closing_link">
                <?php foreach ($this->closing_names as $title => $text) { ?>
                   <li><a href='javascript:void(0);' class='filter_<?php echo $title; ?>' onclick="dealClose('<?php echo $title; ?>')"><?php echo $text; ?></a></li>
                <?php }?>
            </ul>
        </li>
        <li>
            <?php echo TextHelper::_('COBALT_NAMED'); ?>
        </li>
        <li>
            <input name="deal_name" type="text" placeholder="<?php echo TextHelper::_('COBALT_ANYTHING'); ?>" value="<?php echo $this->deal_filter; ?>" class="form-control datatable-searchbox">
        </li>
        <li>
            <div class="ajax_loader"></div>
        </li>
    </ul>

    <small>
        <span id="deals_matched"></span> <?php echo TextHelper::_('COBALT_DEALS_MATCHED'); ?> <?php echo TextHelper::_("COBALT_THERE_ARE"); ?> <?php echo $this->totalDeals; ?> <?php echo TextHelper::_("COBALT_DEALS_IN_ACCOUNT"); ?>
    </small>
<?php echo TemplateHelper::getListEditActions(); ?>
<form method="post" id="list_form" action="<?php echo RouteHelper::_('index.php?view=deals'); ?>">
<table class="table table-hover table-striped data-table" id="deals">
    <?php echo $this->deal_list->render(); ?>
</table>
<input type="hidden" name="list_type" value="deals" />
</form>
