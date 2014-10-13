<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\Helper;

use Cobalt\Factory;
use Cobalt\Model\Menu as MenuModel;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class TemplateHelper
{
    public static function startCompWrap()
    {
        ?>
        <div class="container">
            <div id="com_cobalt">
                <div id="message" style="display:none;"></div>
                    <div id="CobaltModalMessage" class="modal hide fade top-right" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-header small">
                            <h3 id="CobaltModalMessageHeader"></h3>
                        </div>
                        <div id="CobaltModalMessageBody" class="modal-body">
                        <p></p>
                        </div>
                    </div>
                    <div id="google-map" style="display:none;"></div>
                    <div id="edit_note_entry" style="display:none;"></div>
                    <div id="edit_convo_entry" style="display:none;"></div>
                    <div id="document_preview_modal" style="display:none;"></div>
                    <?php echo TemplateHelper::getEventDialog(); ?>
                    <?php echo TemplateHelper::getAvatarDialog(); ?>
                    <script type="text/javascript">var base_url = "<?php echo Factory::getApplication()->get('uri.base.full'); ?>";</script>
                    <?php if (UsersHelper::getLoggedInUser()) : ?>
                    <script type="text/javascript">var userDateFormat = "<?php echo UsersHelper::getDateFormat(false); ?>";</script>
                    <script type="text/javascript">var user_id = "<?php echo UsersHelper::getUserId(); ?>";</script>
                    <?php else : ?>
                    <script type="text/javascript">var userDateFormat = null;</script>
                    <script type="text/javascript">var user_id = 0;</script>
                    <?php endif;
                    if (self::isMobile()) : ?>
                    <div class='page' data-role='page' data-theme='b' id=''>
                    <?php endif; ?>
                    <div id="logoutModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="logoutModal" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h3><?php echo TextHelper::_('COBALT_LOGOUT_HEADER'); ?></h3>
                                </div>
                                <div class="modal-body">
                                    <p><?php echo TextHelper::_('COBALT_LOGOUT_MESSAGE'); ?></p>
                                    <form id="logout-form" class="inline-form block-btn" action="<?php echo RouteHelper::_('index.php?view=logout'); ?>" method="post">
                                        <input type="hidden" name="return" value="<?php echo base64_encode('/'); ?>" />
                                        <?php // echo JHtml::_('form.token'); ?>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn btn-default"><?php echo TextHelper::_('COBALT_CANCEL'); ?></button>
                                    <button type="button" onclick="document.getElementById('logout-form').submit();" class="btn btn-primary"><?php echo TextHelper::_('COBALT_LOGOUT'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
        <?php
    }

    public static function endCompWrap()
    {
        $app = Factory::getApplication();

        if (self::isMobile()) {

            if ($app->input->get('view')!='dashboard') {
                $footer_menu = self::loadFooterMenu();
                echo $footer_menu;
            }
        }
        ?>
                    <div class="modal fade" role="dialog" id="CobaltAjaxModal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h3 id="CobaltAjaxModalHeader">&nbsp;</h3>
                                </div>
                                <div class="modal-body" id="CobaltAjaxModalBody">
                                </div>
                                <div class="modal-footer" id="CobaltAjaxModalFooter">
                                    <button id="CobaltAjaxModalCloseButton" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo ucwords(TextHelper::_('COBALT_CANCEL')); ?></button>
                                    <button id="CobaltAjaxModalSaveButton" onclick="Cobalt.sumbitModalForm(this)" class="btn btn-primary"><?php echo ucwords(TextHelper::_('COBALT_SAVE')); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal hide fade" role="dialog" id="CobaltAjaxModalPreview">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h3 id="CobaltAjaxModalPreviewHeader">&nbsp;</h3>
                                </div>
                                <div class="text-center dmodal-body" id="CobaltAjaxModalPreviewBody">
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if (self::isMobile()) : ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }

    public static function displayLogout()
    {
        $returnURL = base64_encode(RouteHelper::_('index.php?view=dashboard'));
        $string  = '<form class="inline-form" action="'.RouteHelper::_("index.php?task=logout").'" method="post">';
        $string .= '<input type="hidden" name="return" value="'.$returnURL.'" />';
        $string .= '<input type="submit" class="button" value="'.TextHelper::_('COBALT_LOGOUT').'" />';
        $string .= JHtml::_('form.token');
        $string .= '</form>';

        return $string;
    }

    public static function loadToolbar()
    {
        $app = Factory::getApplication();
//load menu
        $menu_model = new MenuModel;
        $list = $menu_model->getMenu();
//Get controller to select active menu item
        $controller = $app->input->get('controller');
        $view = $app->input->get('view');
        $class = "";
//generate html
        $list_html = '<div class="navbar navbar-default navbar-fixed-top" role="navigation">';
        $list_html .= '<div class="container">';
        $list_html .= '<div class="navbar-header">';
        if (StylesHelper::getSiteLogo())
        {
            $list_html .= '<div class="site-logo pull-left">';
            $list_html .= '<img id="site-logo-img" src="'.StylesHelper::getSiteLogo().'" />';
            $list_html .= '</div>';
        }
        $list_html .= '<a id="site-name-link" class="navbar-brand" href="'.Factory::getApplication()->get('uri.base.full').'">';
        $list_html .= StylesHelper::getSiteName();
        $list_html .= '</a>';
        $list_html .= '</div>'; // navbar-header end
        $list_html .= '<ul class="nav navbar-nav">';
        foreach ($list->menu_items as $name)
        {
            $class = $name == $controller || $name == $view ? "active" : "";
            $list_html .= '<li><a class="'.$class.'" href="'.RouteHelper::_('index.php?view='.$name).'">'.ucwords(TextHelper::_('COBALT_MENU_'.strtoupper($name))).'</a></li>';
        }
        $list_html .= '</ul>';
        $list_html .= '<ul class="nav navbar-nav navbar-right">';
        $list_html .= '<li data-toggle="tooltip" title="'.TextHelper::_('COBALT_CREATE_ITEM').'" data-placement="right" class="dropdown">';
        $list_html .= '<a class="feature-btn dropdown-toggle" data-toggle="dropdown" href="#" id="create_button">';
        $list_html .= '<i class="glyphicon glyphicon-plus-sign icon-white"></i>';
        $list_html .= '</a>';
        $list_html .= '<ul class="dropdown-menu">';
        $list_html .= '<li>';
        $list_html .= '<a href="'.RouteHelper::_('index.php?view=companies&layout=edit&format=raw&tmpl=component').'" data-target="#CobaltAjaxModal" data-toggle="modal">';
        $list_html .= '<i class="glyphicon glyphicon-plus-sign"></i> ' . ucwords(TextHelper::_('COBALT_NEW_COMPANY'));
        $list_html .= '</a>';
        $list_html .= '</li>';
        $list_html .= '<li><a href="'.RouteHelper::_('index.php?view=people&layout=edit&format=raw&tmpl=component').'" data-target="#CobaltAjaxModal" data-toggle="modal">';
        $list_html .= '<i class="glyphicon glyphicon-plus-sign"></i> ' . ucwords(TextHelper::_('COBALT_NEW_PERSON'));
        $list_html .= '</a></li>';
        $list_html .= '<li><a href="'.RouteHelper::_('index.php?view=deals&layout=edit&format=raw&tmpl=component').'" data-target="#CobaltAjaxModal" data-toggle="modal">';
        $list_html .= '<i class="glyphicon glyphicon-plus-sign"></i> ' . ucwords(TextHelper::_('COBALT_NEW_DEAL'));
        $list_html .= '</a></li>';
        $list_html .= '<li><a href="'.RouteHelper::_('index.php?view=goals&layout=add').'">';
        $list_html .= '<i class="glyphicon glyphicon-plus-sign"></i> ' . ucwords(TextHelper::_('COBALT_NEW_GOAL'));
        $list_html .= '</a></li>';
        $list_html .= '</ul></li>';
        $list_html .= '<li data-toggle="tooltip" title="'.TextHelper::_('COBALT_VIEW_PROFILE').'" data-placement="bottom">';
        $list_html .= '<a class="block-btn" href="'.RouteHelper::_('index.php?view=profile').'" >';
        $list_html .= '<i class="glyphicon glyphicon-user icon-white"></i>';
        $list_html .= '</a>';
        $list_html .= '</li>';
        $list_html .= '<li data-toggle="tooltip" title="'.TextHelper::_('COBALT_SUPPORT').'" data-placement="bottom">';
        $list_html .= '<a class="block-btn" href="http://www.cobaltcrm.org/" target="_blank">';
        $list_html .= '<i class="glyphicon glyphicon-question-sign icon-white"></i>';
        $list_html .= '</a>';
        $list_html .= '</li>';
        $list_html .= '<li data-toggle="tooltip" title="'.TextHelper::_('COBALT_SEARCH').'" data-placement="bottom">';
        $list_html .= '<a class="block-btn" href="#" onclick="Cobalt.showSiteSearch(); return false;">';
        $list_html .= '<i class="glyphicon glyphicon-search icon-white"></i>';
        $list_html .= '</a>';
        $list_html .= '</li>';
        if (UsersHelper::isAdmin())
        {
            $list_html .= '<li data-toggle="tooltip" title="'.TextHelper::_('COBALT_ADMINISTRATOR_CONFIGURATION').'" data-placement="bottom">';
            $list_html .= '<a class="block-btn" href="'.RouteHelper::_('index.php?view=cobalt').'" >';
            $list_html .= '<i class="glyphicon glyphicon-cog icon-white"></i>';
            $list_html .= '</a>';
            $list_html .= '</li>';
        }
        if (UsersHelper::getLoggedInUser() && !(Factory::getApplication()->input->get('view')=="print"))
        {
            $returnURL = base64_encode(RouteHelper::_('index.php?view=dashboard'));
            $list_html .= '<li data-toggle="tooltip" title="'.TextHelper::_('COBALT_LOGOUT').'" data-placement="bottom">';
            $list_html .= '<a class="block-btn" data-toggle="modal" href="#logoutModal">';
            $list_html .= '<i class="glyphicon glyphicon-off icon-white"></i>';
            $list_html .= '</a>';
            $list_html .= '</li>';
        }
        $list_html .= '</ul>';
        $list_html .= '</div>';
        $list_html .= '<div class="container">';
        $list_html .= '<div style="display:none;" class="pull-right col-xs-3" id="site_search">';
        $list_html .= '<form action="index.php" id="site_search_form">';
        $list_html .= '<input type="text" class="form-control site_search" name="site_search_input" id="site_search_input" placeholder="'.TextHelper::_('COBALT_SEARCH_SITE').'" value="" />';
        $list_html .= '<input type="hidden" name="view" />';
        $list_html .= '<input type="hidden" name="id" />';
        $list_html .= '<input type="hidden" name="layout" />';
        $list_html .= '</form>';
        $list_html .= '</div>';
        $list_html .= '</div>';
        $list_html .= '</div>';
//return html
        echo $list_html;
    }

    public static function loadFooterMenu()
    {
        $str = '<div data-role="footer" data-position="fixed" data-id="cobaltFooter">
                    <div data-role="navbar" data-iconpos="top">
                        <ul>
                            <li><a data-icon="agenda" data-iconpos="top" id="agendaButton" href="'.RouteHelper::_('index.php?view=events').'">'.ucwords(TextHelper::_('COBALT_AGENDA')).'</a></li>
                            <li><a data-icon="deals" data-iconpos="top" id="dealsButton" href="'.RouteHelper::_('index.php?view=deals').'">'.ucwords(TextHelper::_('COBALT_DEALS_HEADER')).'</a></li>
                            <li><a data-icon="leads" data-iconpos="top" id="leadsButton" href="'.RouteHelper::_('index.php?view=people&type=leads').'">'.ucwords(TextHelper::_('COBALT_LEADS')).'</a></li>
                            <li><a data-icon="contacts" data-iconpos="top" id="contactsButton" href="'.RouteHelper::_('index.php?view=people&type=not_leads').'">'.ucwords(TextHelper::_('COBALT_CONTACTS')).'</a></li>
                            <li><a data-icon="companies" data-iconpos="top" id="CompaniesButton" href="'.RouteHelper::_('index.php?view=companies').'">'.ucwords(TextHelper::_('COBALT_COMPANIES')).'</a></li>
                        </ul>
                    </div>
                </div>';

        return $str;
    }

    public static function loadReportMenu()
    {
        $app = Factory::getApplication();
        $activeLayout = $app->input->get('layout');

        $layouts = array('dashboard','sales_pipeline','source_report','roi_report','deal_milestones','notes','custom_reports');
        $str = "<ul class='nav nav-tabs'>";

        foreach ($layouts as $layout) {
            $languageString = strtoupper('COBALT_'.$layout);
            if ($layout == 'dashboard' ) $layout = 'default';
            $class = $activeLayout == $layout ? 'class=active' : '';
            $str .= '<li '.$class.'><a href="'.RouteHelper::_('index.php?view=reports&layout='.$layout).'" >'.ucwords(TextHelper::_($languageString)).'</a></li>';
        }

        $str .= "</ul>";

        return $str;
    }

    public static function getEventDialog()
    {
        $html  = "";
        $html .= '<div id="new_event_dialog" style="display:none;">';
            $html .= '<div class="new_events">';
                $html .= '<a href="javasript:void(0);" class="task" onclick="Cobalt.addTaskEvent(\'task\')">'.TextHelper::_('COBALT_ADD_TASK').'</a><a  href="javasript:void(0);" class="event" onclick="Cobalt.addTaskEvent(\'event\')">'.TextHelper::_('COBALT_ADD_EVENT').'</a><a href="javasript:void(0);" class="complete" onclick="jQuery(\'#new_event_dialog\').dialog(\'close\');">'.TextHelper::_('COBALT_DONE').'</a>';
            $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    public static function getAvatarDialog()
    {
        $html = "<iframe id='avatar_frame' name='avatar_frame' style='display:none;border:0px;width:0px;height:0px;opacity:0;'></iframe>";
        $html .= '<div class="filters" id="avatar_upload_dialog" style="display:none;">';
            $html .= "<div id='avatar_message'>".TextHelper::_('COBALT_SELECT_A_FILE_TO_UPLOAD').'</div>';
                        $html .= '<div class="input_upload_button" >';
                            $html .= '<form id="avatar_upload_form" method="POST" enctype="multipart/form-data" target="avatar_frame" >';
                                $html .= "<input type='hidden' name='option' value='com_cobalt' />";
                                $html .= '<input class="button" type="button" id="upload_avatar_button" value="'.TextHelper::_('COBALT_UPLOAD_FILE').'" />';
                                $html .= '<input type="file" id="upload_input_invisible_avatar" name="avatar" />';
                            $html .= '</form>';
                        $html .= '</div>';
        $html .= "</div>";

        return $html;
    }

    public static function isMobile()
    {
        return false;
        $app = Factory::getApplication();
        $mobile_detect = new MobileHelper();
        $mobile_auto = $mobile_detect->isMobile();
        $mobile_manual = $app->input->get('mobile');

        if ( !is_array($_SESSION) ) {
            session_start();
        }

        if ($mobile_manual) {
            if ($mobile_manual == "no") {
                $app->input->set('mobile','no');
                $_SESSION['mobile'] = "no";
                $mobile = false;
            } elseif ($mobile_manual == "yes") {
                $app->input->set('mobile','yes');
                $_SESSION['mobile'] = "yes";
                $mobile = true;
            } else {
                $app->input->set('mobile','no');
                $_SESSION['mobile'] = "no";
                $mobile = false;
            }
        } elseif ($mobile_auto) {
            $app->input->set('mobile','yes');
            $_SESSION['mobile'] = "yes";
            $mobile = true;
        } else {
            $app->input->set('mobile','no');
            $_SESSION['mobile'] = "no";
            $mobile = false;
        }

        return $mobile;
    }

    public static function loadJavascriptLanguage()
    {
        TextHelper::script('COBALT_PLEASE_SELECT_A_USER');
        TextHelper::script('COBALT_SUCCESS_MESSAGE');
        TextHelper::script('COBALT_ADD_NEW_NOTE');
        TextHelper::script('COBALT_ADD');
        TextHelper::script('COBALT_SUCCESS_MESSAGE');
        TextHelper::script('COBALT_GENERIC_UPDATED');
        TextHelper::script('COBALT_DEALS_BY_STAGE');
        TextHelper::script('COBALT_TOTAL');
        TextHelper::script('COBALT_DEALS_BY_STATUS');
        TextHelper::script('COBALT_REVENUE_FROM_LEAD_SOURCES');
        TextHelper::script('COBALT_CURRENCY');
        TextHelper::script('COBALT_YEARLY_COMMISSIONS');
        TextHelper::script('COBALT_MONTHLY_COMMISSIONS');
        TextHelper::script('COBALT_YEARLY_REVENUE');
        TextHelper::script('COBALT_MONTHLY_REVENUE');
        TextHelper::script('COBALT_WEEK');
        TextHelper::script('COBALT_DELETE_GOALS');
        TextHelper::script('COBALT_DELETE_GOAL_CONFIRMATION');
        TextHelper::script('COBALT_TODAY');
        TextHelper::script('COBALT_MONTH');
        TextHelper::script('COBALT_WEEK');
        TextHelper::script('COBALT_DAY');
        TextHelper::script('COBALT_DAYS');
        TextHelper::script('COBALT_EDIT_NOTE');
        TextHelper::script('COBALT_EDIT_NOTES');
        TextHelper::script('COBALT_JANUARY');
        TextHelper::script('COBALT_FEBRUARY');
        TextHelper::script('COBALT_MARCH');
        TextHelper::script('COBALT_APRIL');
        TextHelper::script('COBALT_MAY');
        TextHelper::script('COBALT_JUNE');
        TextHelper::script('COBALT_JULY');
        TextHelper::script('COBALT_AUGUST');
        TextHelper::script('COBALT_SEPTEMBER');
        TextHelper::script('COBALT_OCTOBER');
        TextHelper::script('COBALT_NOVEMBER');
        TextHelper::script('COBALT_DECEMBER');
        TextHelper::script('COBALT_JAN');
        TextHelper::script('COBALT_FEB');
        TextHelper::script('COBALT_MAR');
        TextHelper::script('COBALT_APR');
        TextHelper::script('COBALT_MAY');
        TextHelper::script('COBALT_JUN');
        TextHelper::script('COBALT_JUL');
        TextHelper::script('COBALT_AUG');
        TextHelper::script('COBALT_SEP');
        TextHelper::script('COBALT_OCT');
        TextHelper::script('COBALT_NOV');
        TextHelper::script('COBALT_DEC');
        TextHelper::script('COBALT_SUNDAY');
        TextHelper::script('COBALT_MONDAY');
        TextHelper::script('COBALT_TUESDAY');
        TextHelper::script('COBALT_WEDNESDAY');
        TextHelper::script('COBALT_THURSDAY');
        TextHelper::script('COBALT_FRIDAY');
        TextHelper::script('COBALT_SATURDAY');
        TextHelper::script('COBALT_SUN');
        TextHelper::script('COBALT_MON');
        TextHelper::script('COBALT_TUE');
        TextHelper::script('COBALT_WED');
        TextHelper::script('COBALT_THU');
        TextHelper::script('COBALT_FRI');
        TextHelper::script('COBALT_SAT');
        TextHelper::script('COBALT_EDIT');
        TextHelper::script('COBALT_DELETE_CONFIRMATION');
        TextHelper::script('COBALT_DELETE_PERSON_FROM_DEAL_CONFIRM');
        TextHelper::script('COBALT_MARK_COMPLETE');
        TextHelper::script('COBALT_MARK_INCOMPLETE');
        TextHelper::script('COBALT_SUCCESSFULLY_REMOVED_EVENT');
        TextHelper::script('COBALT_ITEM_SUCCESSFULLY_UPDATED');
        TextHelper::script('COBALT_OK');
        TextHelper::script('COBALT_ADDED');
        TextHelper::script('COBALT_DELETED');
        TextHelper::script('COBALT_VERIFY_ALERT');
        TextHelper::script('COM_CMERY_ARE_YOU_SURE_DELETE_REPORT');
        TextHelper::script('COBALT_DEALS_BY_STAGE');
        TextHelper::script('COBALT_DEALS_BY_STATUS');
        TextHelper::script('COBALT_TOTAL');
        TextHelper::script('COBALT_UPDATE');
        TextHelper::script('COBALT_UPLOADING');
        TextHelper::script('COBALT_YOUR_DOCUMENT_IS_BEING_UPLOADED');
        TextHelper::script('COBALT_MESSAGE_DETAILS');
        TextHelper::script('COBALT_CLICK_TO_EDIT');
        TextHelper::script('COBALT_UPDATED_PRIMARY_CONTACT');
        TextHelper::script('COBALT_MANAGE_MAILING_LISTS');
        TextHelper::script('COBALT_MAILING_LIST_LINKS');
        TextHelper::script('COBALT_REMOVE');
        TextHelper::script('COBALT_ADD');
        TextHelper::script('COBALT_EMAIL');
        TextHelper::script('COBALT_ACTIVE_DEAL');
        TextHelper::script('COBALT_START_TYPING_DEAL');
        TextHelper::script('COBALT_START_TYPING_PERSON');
        TextHelper::script('COBALT_SUCCESSFULLY_SHARED_ITEM');
        TextHelper::script('COBALT_SUCCESSFULLY_UNSHARED_ITEM');
        TextHelper::script('COBALT_SHARE_ITEM');
        TextHelper::script('COBALT_STAGE');
        TextHelper::script('COBALT_EDITING_EVENT');
        TextHelper::script('COBALT_EDITING_TASK');
        TextHelper::script('COBALT_ADDING_EVENT');
        TextHelper::script('COBALT_ADDING_TASK');
        TextHelper::script('COBALT_CONTACTS');
        TextHelper::script('COBALT_COMPANY');
        TextHelper::script('COBALT_DEALS_HEADER');
        TextHelper::script('COBALT_PEOPLE');
        TextHelper::script('COBALT_ERROR_MARK_ITEM_COMPLETE');
        TextHelper::script('COBALT_DATATABLE_NO_DATA_AVAILABLE_IN_TABLE');
        TextHelper::script('COBALT_DATATABLE_NO_MATCHING_RECORDS_FOUND');
        TextHelper::script('COBALT_DATATABLE_SHOWING_START_TO_END_ENTRIES');
        TextHelper::script('COBALT_DATATABLE_SHOWING_ZERO_TO_ZERO_OF_ZERO_ENTRIES');
        TextHelper::script('COBALT_DATATABLE_FILTERED_TOTAL_ENTRIES');
        TextHelper::script('COBALT_DATATABLE_INFO_THOUSANDS');
        TextHelper::script('COBALT_DATATABLE_SHOW_MENU_ENTRIES');
        TextHelper::script('COBALT_DATATABLE_LOADING');
        TextHelper::script('COBALT_DATATABLE_PROCESSING');
        TextHelper::script('COBALT_DATATABLE_SEARCH');
        TextHelper::script('COBALT_DATATABLE_FIRST_PAGE');
        TextHelper::script('COBALT_DATATABLE_LAST_PAGE');
        TextHelper::script('COBALT_DATATABLE_NEXT_PAGE');
        TextHelper::script('COBALT_DATATABLE_PREVIOUS_PAGE');
        TextHelper::script('COBALT_DATATABLE_ACTIVATE_TO_SORT_COLUMN_ASCENDING');
        TextHelper::script('COBALT_DATATABLE_ACTIVATE_TO_SORT_COLUMN_DESCENDING');
    }

    public static function getListEditActions()
    {
        $list_html  = "";

        $list_html .= "<div id='list_edit_actions'>";
        $list_html .= '<ul class="list-inline">';
        $list_html .= '<li>' . TextHelper::_('COBALT_PERFORM') . '</li>';
        $list_html .= '<li class="dropdown">';
        $list_html .= '<a class="dropdown-toggle" href="#" data-toggle="dropdown" role="button" >' . TextHelper::_('COBALT_ACTIONS') . '</a>';
            $list_html .= '<ul class="dropdown-menu" role="menu" aria-labelledby="">';
                if (UsersHelper::canDelete())
                {
                    $list_html .= '<li><a onclick="Cobalt.deleteListItems()">' . TextHelper::_('COBALT_DELETE') . '</a></li>';
                }
            $list_html .= '</ul>';
        $list_html .= '</li>';
        $list_html .= '<li>' . TextHelper::_('COBALT_ON_THE') . "<span id='items_checked'></span> " . TextHelper::_('COBALT_ITEMS') . '</li>';
        $list_html .= '</ul>';
        $list_html .= "</div>";

        return $list_html;
    }

    public static function getEventListEditActions()
    {
        $list_html  = "";

        $list_html .= "<div id='list_edit_actions'>";
        $list_html .= TextHelper::_('COBALT_PERFORM')."<a class='dropdown' id='list_edit_actions_dropdown_link'>".TextHelper::_('COBALT_ACTIONS')."</a>".TextHelper::_('COBALT_ON_THE')."<span id='items_checked'></span> ".TextHelper::_('COBALT_ITEMS');
        $list_html .= '<div class="filters" id="list_edit_actions_dropdown">';
        $list_html .= '<ul>';
        $list_html .= '<li><a onclick="Cobalt.deleteListItems()">'.TextHelper::_('COBALT_DELETE').'</a></li>';
        $list_html .= '</ul>';
        $list_html .= "</div>";
        $list_html .= "</div>";

        return $list_html;
    }

    public static function showMessages()
    {
        $app = Factory::getApplication();
        $document = $app->getDocument();
        $messageTypes = $app->getMessageQueue();
        $js = '';
        if (is_array($messageTypes) && $messageTypes)
        {
            foreach ($messageTypes as $type => $messages)
            {
                if (is_array($messages) && $messages)
                {
                    foreach ($messages as $message)
                    {
                        $js .= "Cobalt.modalMessage('', '" . $message . "', '" . $type . "');"."\n";
                    }
                }
            }
            $document->addScriptDeclaration("
jQuery(function() {
" . $js . "
});
");
            $app->clearMessageQueue();
        }
    }
}
