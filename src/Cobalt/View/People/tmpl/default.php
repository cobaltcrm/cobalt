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

    <div class="modal fade" id="personModal" tabindex="-1" role="dialog" aria-labelledby="personModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content"></div>
        </div>
    </div>

    <div class="btn-group pull-right">

        <?php if ( UsersHelper::canExport() ): ?>
            <button type="button" class="btn btn-default" href="<?php echo RouteHelper::_('index.php?view=people&layout=edit&format=raw&tmpl=component'); ?>" data-target="#personModal" data-toggle="modal"><i class="glyphicon glyphicon-plus icon-white"></i> <?php echo TextHelper::_('COBALT_PEOPLE_ADD'); ?></button>

            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                <span class="caret"></span>
                <span class="sr-only">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li>
                    <a href="<?php echo RouteHelper::_('index.php?view=import&import_type=people'); ?>">
                        <i class="glyphicon glyphicon-arrow-up"></i> <?php echo TextHelper::_('COBALT_IMPORT_PEOPLE'); ?>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" onclick="Cobalt.exportCSV()">
                        <i class="glyphicon glyphicon glyphicon-arrow-down"></i> <?php echo TextHelper::_('COBALT_EXPORT_PEOPLE'); ?>
                    </a>
                </li>
            </ul>
        <?php else: ?>
            <a rel="tooltip" title="<?php echo TextHelper::_('COBALT_PEOPLE_ADD'); ?>" data-placement="bottom" class="btn btn-default" role="button" href="<?php echo RouteHelper::_('index.php?view=people&layout=edit&format=raw&tmpl=component'); ?>" data-target="#personModal" data-toggle="modal"><i class="glyphicon glyphicon-plus icon-white"></i> <?php echo TextHelper::_('COBALT_PEOPLE_ADD'); ?></a>
            <a rel="tooltip" title="<?php echo TextHelper::_('COBALT_IMPORT_PEOPLE'); ?>" data-placement="bottom"  class="btn btn-default" href="<?php echo RouteHelper::_('index.php?view=import&import_type=people'); ?>">
                <i class="glyphicon glyphicon-arrow-up"></i>
            </a>
        <?php endif; ?>

    </div>

    <h1><?php echo ucwords(TextHelper::_('COBALT_PEOPLE_HEADER')); ?></h1>
</div>
<ul class="list-inline filter-sentence">
    <li><span><?php echo TextHelper::_('COBALT_SHOW'); ?></span></li>
    <li class="dropdown">
        <a class="dropdown-toggle update-toggle-text" data-toggle="dropdown" role="button" id="people_type_link" href="javascript:void(0);"><span class="dropdown-label"><?php echo $this->people_type_name; ?><span></a>
        <ul class="dropdown-menu" role="menu" aria-labelledby="people_type_link" data-filter="item">
            <?php foreach ($this->people_types as $title => $text) { ?>
            <li>
                <a href="#" class="filter_<?php echo $title; ?>" data-filter-value="<?php echo $title; ?>">
                    <?php echo $text; ?>
                </a>
            </li>
            <?php } ?>
        </ul>
    </li>
    <li><span><?php echo TextHelper::_('COBALT_OWNED_BY'); ?></span></li>
    <li class="dropdown">
        <a class="dropdown-toggle update-toggle-text" href="#" data-toggle="dropdown" role="button" id="people_user_link">
            <span class="dropdown-label">
                <?php echo $this->user_name; ?>
            </span>
        </a>
        <ul class="dropdown-menu" role="menu" aria-labelledby="people_user_link" data-filter="ownertype">
            <li>
                <a href="#" data-filter-value="member:<?php echo $this->user_id; ?>">
                    <?php echo TextHelper::_('COBALT_ME'); ?>
                </a>
            </li>
            <?php if ($this->member_role != 'basic') { ?>
                 <li>
                    <a href="#" data-filter-value="all">
                        <?php echo TextHelper::_('COBALT_ALL_USERS'); ?>
                    </a>
                </li>
            <?php } ?>
            <?php
                if ($this->member_role == 'exec') {
                    if ( count($this->teams) > 0 ) {
                        foreach ($this->teams as $team) { ?>
                <li>
                    <a href="#" data-filter-value="team:<?php echo $team['team_id']; ?>">
                        <?php echo $team['team_name'].TextHelper::_('COBALT_TEAM_APPEND') ?>
                    </a>
                </li>";
            <?php       }
                    }
                }
                if ( count($this->users) > 0 ) {
                    foreach ($this->users as $user) { ?>
                <li>
                    <a href="#" data-filter-value="member:<?php echo $user['id']; ?>">
                    <?php echo $user['first_name']."  ".$user['last_name'] ?>
                    </a>
                </li>
            <?php   }
                }
            ?>
        </ul>
    </li>
    <li><span><?php echo TextHelper::_('COBALT_WHO'); ?></span></li>
    <li class="dropdown">
        <a class="dropdown-toggle update-toggle-text" href="#" data-toggle="dropdown" role="button" id="people_stages_link">
            <span class="dropdown-label"><?php echo $this->stages_name; ?></span>
        </a>
        <ul class="dropdown-menu" role="menu" aria-labelledby="people_stages_link" data-filter="stage">
            <?php foreach ($this->stages as $title => $text) { ?>
            <li>
                <a href="#" data-filter-value="<?php echo $title; ?>"><?php echo $text ?></a>
            </li>
            <?php } ?>
        </ul>
    </li>
    <li><span><?php echo TextHelper::_('COBALT_AND_WITH_STATUS'); ?></span></li>
    <li class="dropdown">
        <a class="update-toggle-text dropdown-toggle" href="javascript:void(0);" data-toggle="dropdown" role="button" id="people_status_link"><span class="dropdown-label"><?php echo $this->status_name; ?></span></a>
        <ul class="dropdown-menu" role="menu" aria-labelledby="people_status_link" data-filter="status">
            <li>
                <a class="filter_any">
                    <?php echo TextHelper::_('COBALT_ANY_STATUS'); ?>
                </a>
            </li>
            <?php foreach ($this->status_list as $key => $status) { ?>
            <li>
                <a href="#" data-filter-value="<?php echo $status['id']; ?>">
                    <?php echo $status['name']; ?>
                </a>
            </li>
            <?php } ?>
        </ul>
    </li>
    <li>
        <span><?php echo TextHelper::_('COBALT_NAMED'); ?></span>
    </li>
    <li>
        <input class="form-control filter_input datatable-searchbox" name="person_name" type="text" placeholder="<?php echo TextHelper::_('COBALT_ANYTHING'); ?>" value="<?php echo $this->people_filter; ?>">
    </li>
    <li class="filter_sentence">
        <div class="ajax_loader"></div>
    </li>
</ul>

<?php echo TemplateHelper::getListEditActions(); ?>
<form method="post" id="list_form" action="<?php echo RouteHelper::_('index.php?view=people'); ?>">
    <table class="table table-striped table-hover data-table table-bordered" id="people">
        <?php echo $this->people_list->render(); ?>
    </table>
    <input type="hidden" name="list_type" value="people" />
</form>