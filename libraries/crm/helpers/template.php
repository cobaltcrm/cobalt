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
defined( '_JEXEC' ) or die( 'Restricted access' );

 include(JPATH_SITE.'/libraries/crm/helpers/mdetect.php');
 jimport('joomla.application.component.model');

 class CobaltHelperTemplate
 {
    public static function startCompWrap()
    {
          echo '<div class="container">';

          echo '<div id="com_cobalt">';
          echo '<div id="message" style="display:none;"></div>';

          echo '<div id="CobaltModalMessage" class="modal hide fade top-right" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-header small">
            <h3 id="CobaltModalMessageHeader"></h3>
          </div>
          <div id="CobaltModalMessageBody" class="modal-body">
            <p></p>
          </div>
        </div>';

            echo ' <div id="alertMessage" class="page-alert alert alert-success">
                <div id="alertMessageHeader"></div>
                <div id="alertMessageBody"></div>
              </div>';

          echo '<div id="google-map" style="display:none;"></div>';
          echo '<div id="edit_note_entry" style="display:none;"></div>';
          echo '<div id="edit_convo_entry" style="display:none;"></div>';
          echo '<div id="document_preview_modal" style="display:none;"></div>';
          echo CobaltHelperTemplate::getEventDialog();
          echo CobaltHelperTemplate::getAvatarDialog();
          echo '<script type="text/javascript">var base_url = "'.JURI::base().'";</script>';

        if (CobaltHelperUsers::getLoggedInUser()) {
            echo '<script type="text/javascript">var userDateFormat = "'.CobaltHelperUsers::getDateFormat(FALSE).'";</script>';
            echo '<script type="text/javascript">var user_id = "'.CobaltHelperUsers::getUserId().'";</script>';
        } else {
            echo '<script type="text/javascript">var userDateFormat = null;</script>';
            echo '<script type="text/javascript">var user_id = 0;</script>';
        }

            if (self::isMobile()) {
                echo "<div class='page' data-role='page' data-theme='b' id=''>";
            }

        echo '<div id="logout-modal" class="modal hide fade">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3>'.CRMText::_('COBALT_LOGOUT_HEADER').'</h3>
                  </div>
                  <div class="modal-body">
                    <p>'.CRMText::_('COBALT_LOGOUT_MESSAGE').'</p>
                  </div>
                  <div class="modal-footer">
                    <a href="#" onclick="hideLogoutModal();" class="btn">'.CRMText::_('COBALT_CANCEL').'</a>
                    <a href="#" onclick="performLogout();" class="btn btn-primary">'.CRMText::_('COBALT_LOGOUT').'</a>
                  </div>
                </div>';
    }

    public static function displayLogout()
    {
        $returnURL = base64_encode(JRoute::_('index.php?view=dashboard'));
        $string  = '<form class="inline-form" action="index.php?controller=logout" method="post">';
        $string .= '<input type="hidden" name="return" value="'.$returnURL.'" />';
        $string .= '<input type="submit" class="button" value="'.CRMText::_('COBALT_LOGOUT').'" />';
        $string .= JHtml::_('form.token');
        $string .= '</form>';

        return $string;
    }

    public static function loadToolbar()
    {
        $app = JFactory::getApplication();

        //load menu
        $menu_model = new CobaltModelMenu();
        $list = $menu_model->getMenu();

        //Get controller to select active menu item
        $controller = $app->input->get('controller');
        $view = $app->input->get('view');
        $class = "";

        //generate html
        $list_html  = '<div class="navbar navbar-fixed-top">';
        $list_html .= '<div class="navbar-inner"><div class="container">';
        $list_html .= "<div class='site-logo'>";
        $list_html .= "<img id='site-logo-img' src='".CobaltHelperStyles::getSiteLogo()."' />";
        $list_html .= '</div>';
        $list_html .= '<a id="site-name-link" class="brand" href="index.php">'.CobaltHelperStyles::getSiteName().'</a>';
        $list_html .= '<ul class="nav">';
        foreach ($list->menu_items as $name) {
            $class = $name == $controller || $name == $view ? "active" : "";
            $list_html .= '<li><a class="'.$class.'" href="'.JRoute::_('index.php?view='.$name).'">'.ucwords(CRMText::_('COBALT_MENU_'.strtoupper($name))).'</a></li>';
        }
        $list_html .= '</ul>';
        $list_html .= '<div class="pull-right dropdown">';
        $list_html .= '<a rel="tooltip" title="'.CRMText::_('COBALT_CREATE_ITEM').'" data-placement="bottom" class="feature-btn" href="javascript:void(0);" id="create_button" ><i class="icon-plus icon-white"></i></a>';
        $list_html .= '<div id="create" style="display:none;">';
        $list_html .= "<ul>";
        $list_html .= '<li><a rel="tooltip" title="'.CRMText::_('COBALT_ADD_COMPANY').'" data-placement="bottom"  href="'.JRoute::_('index.php?view=companies&layout=edit').'">'.ucwords(CRMText::_('COBALT_NEW_COMPANY')).'</a></li>';
        $list_html .= '<li><a rel="tooltip" title="'.CRMText::_('COBALT_ADD_PERSON').'" data-placement="bottom" href="'.JRoute::_('index.php?view=people&layout=edit').'">'.ucwords(CRMText::_('COBALT_NEW_PERSON')).'</a></li>';
        $list_html .= '<li><a rel="tooltip" title="'.CRMText::_('COBALT_ADD_DEAL').'" data-placement="bottom" href="'.JRoute::_('index.php?view=deals&layout=edit').'">'.ucwords(CRMText::_('COBALT_NEW_DEAL')).'</a></li>';
        $list_html .= '<li><a rel="tooltip" title="'.CRMText::_('COBALT_ADD_GOAL').'" data-placement="bottom" href="'.JRoute::_('index.php?view=goals&layout=add').'">'.ucwords(CRMText::_('COBALT_NEW_GOAL')).'</a></li>';
        $list_html .= '</ul>';
        $list_html .= '</div>';
        $list_html .= '<a rel="tooltip" title="'.CRMText::_('COBALT_VIEW_PROFILE').'" data-placement="bottom" class="block-btn" href="'.JRoute::_('index.php?view=profile').'" ><i class="icon-user icon-white"></i></a>';
        $list_html .= '<a rel="tooltip" title="'.CRMText::_('COBALT_ENTER_FULLSCREEN').'" data-placement="bottom" class="block-btn" href="javascript:void(0);" onclick="toggleFullScreen();" ><i class="icon-fullscreen icon-white"></i></a>';
        $list_html .= '<a rel="tooltip" title="'.CRMText::_('COBALT_SUPPORT').'" data-placement="bottom" class="block-btn" href="http://www.cobaltcrm.org/support"><i class="icon-question-sign icon-white"></i></a>';
        $list_html .= '<a rel="tooltip" title="'.CRMText::_('COBALT_SEARCH').'" data-placement="bottom" class="block-btn" href="javascript:void(0);"><i onclick="showSiteSearch();" class="icon-search icon-white"></i></a>';

        if ( CobaltHelperUsers::isAdmin() ) {
            $list_html .= '<a rel="tooltip" title="'.CRMText::_('COBALT_ADMINISTRATOR_CONFIGURATION').'" data-placement="bottom" class="block-btn" href="'.JRoute::_('index.php?view=cobalt').'" ><i class="icon-cog icon-white"></i></a>';
        }

        if ( CobaltHelperUsers::getLoggedInUser() && !(JFactory::getApplication()->input->get('view')=="print") ) {
            $returnURL = base64_encode(JRoute::_('index.php?view=dashboard'));
            $list_html .= '<form id="logout-form" class="inline-form block-btn" action="index.php?controller=logout" method="post">';
            $list_html .= '<input type="hidden" name="return" value="'.$returnURL.'" />';
            $list_html .= '<a class="block-btn" rel="tooltip" title="'.CRMText::_('COBALT_LOGOUT').'" data-placement="bottom" href="javascript:void(0);" onclick="confirmLogout();" ><i class="icon-off icon-white"></i></a>';
            $list_html .= JHtml::_('form.token');
            $list_html .= '</form>';
        }

        $list_html .= '</div>';
        $list_html .= '</div>';
        $list_html .= '<div style="display:none;" class="pull-right" id="site_search">';
        $list_html .= '<input class="inputbox site_search" name="site_search_input" id="site_search_input" placeholder="'.CRMText::_('COBALT_SEARCH_SITE').'" value="" />';
        $list_html .= '</div></div></div>';

        //return html
        echo $list_html;
    }

    public static function endCompWrap()
    {
        $app = JFactory::getApplication();

        if (self::isMobile()) {

            if ($app->input->get('view')!='dashboard') {
                $footer_menu = self::loadFooterMenu();
                echo $footer_menu;
            }
        }

            echo ' <div class="modal hide fade" role="dialog" id="CobaltAjaxModal">
                       <div class="modal-header">
                           <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                           <h3 id="CobaltAjaxModalHeader">
                               &nbsp;
                           </h3>
                       </div>
                       <div class="modal-body" id="CobaltAjaxModalBody">
                       </div>
                       <div class="modal-footer" id="CobaltAjaxModalFooter">
                           <button id="CobaltAjaxModalCloseButton" class="btn" data-dismiss="modal" aria-hidden="true">'.ucwords(CRMText::_('COBALT_CANCEL')).'</button>
                           <button id="CobaltAjaxModalSaveButton" onclick="saveModal(this)" class="btn btn-primary">'.ucwords(CRMText::_('COBALT_SAVE')).'</button>
                       </div>
                    </div>';

            echo ' <div class="modal hide fade" role="dialog" id="CobaltAjaxModalPreview">
               <div class="modal-header">
                   <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                   <h3 id="CobaltAjaxModalPreviewHeader">
                       &nbsp;
                   </h3>
               </div>
               <div class="text-center dmodal-body" id="CobaltAjaxModalPreviewBody">
               </div>
            </div>';
            //Ends starting page div
            echo '</div>';
        echo '</div>';

    }

    public static function loadFooterMenu()
    {
        $str = '<div data-role="footer" data-position="fixed" data-id="cobaltFooter">
                    <div data-role="navbar" data-iconpos="top">
                        <ul>
                            <li><a data-icon="agenda" data-iconpos="top" id="agendaButton" href="'.JRoute::_('index.php?view=events').'">'.ucwords(CRMText::_('COBALT_AGENDA')).'</a></li>
                            <li><a data-icon="deals" data-iconpos="top" id="dealsButton" href="'.JRoute::_('index.php?view=deals').'">'.ucwords(CRMText::_('COBALT_DEALS_HEADER')).'</a></li>
                            <li><a data-icon="leads" data-iconpos="top" id="leadsButton" href="'.JRoute::_('index.php?view=people&type=leads').'">'.ucwords(CRMText::_('COBALT_LEADS')).'</a></li>
                            <li><a data-icon="contacts" data-iconpos="top" id="contactsButton" href="'.JRoute::_('index.php?view=people&type=not_leads').'">'.ucwords(CRMText::_('COBALT_CONTACTS')).'</a></li>
                            <li><a data-icon="companies" data-iconpos="top" id="CompaniesButton" href="'.JRoute::_('index.php?view=companies').'">'.ucwords(CRMText::_('COBALT_COMPANIES')).'</a></li>
                        </ul>
                    </div>
                </div>';

        return $str;
    }

    public static function loadReportMenu()
    {
        $app = JFactory::getApplication();
        $activeLayout = $app->input->get('layout');

        $layouts = array('dashboard','sales_pipeline','source_report','roi_report','deal_milestones','notes','custom_reports');
        $str = "<ul class='nav nav-tabs'>";

        foreach ($layouts as $layout) {
            $languageString = strtoupper('COBALT_'.$layout);
            if ($layout == 'dashboard' ) $layout = 'default';
            $class = $activeLayout == $layout ? 'class=active' : '';
            $str .= '<li '.$class.'><a href="'.JRoute::_('index.php?view=reports&layout='.$layout).'" >'.ucwords(CRMText::_($languageString)).'</a></li>';
        }

        $str .= "</ul>";

        return $str;
    }

    public static function getEventDialog()
    {
        $html  = "";
        $html .= '<div id="new_event_dialog" style="display:none;">';
            $html .= '<div class="new_events">';
                $html .= '<a href="javasript:void(0);" class="task" onclick="addTaskEvent(\'task\')">'.CRMText::_('COBALT_ADD_TASK').'</a><a  href="javasript:void(0);" class="event" onclick="addTaskEvent(\'event\')">'.CRMText::_('COBALT_ADD_EVENT').'</a><a href="javasript:void(0);" class="complete" onclick="jQuery(\'#new_event_dialog\').dialog(\'close\');">'.CRMText::_('COBALT_DONE').'</a>';
            $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    public static function getAvatarDialog()
    {
        $html = "<iframe id='avatar_frame' name='avatar_frame' style='display:none;border:0px;width:0px;height:0px;opacity:0;'></iframe>";
        $html .= '<div class="filters" id="avatar_upload_dialog" style="display:none;">';
            $html .= "<div id='avatar_message'>".CRMText::_('COBALT_SELECT_A_FILE_TO_UPLOAD').'</div>';
                        $html .= '<div class="input_upload_button" >';
                            $html .= '<form id="avatar_upload_form" method="POST" enctype="multipart/form-data" target="avatar_frame" >';
                                $html .= "<input type='hidden' name='option' value='com_cobalt' />";
                                $html .= '<input class="button" type="button" id="upload_avatar_button" value="'.CRMText::_('COBALT_UPLOAD_FILE').'" />';
                                $html .= '<input type="file" id="upload_input_invisible_avatar" name="avatar" />';
                            $html .= '</form>';
                        $html .= '</div>';
        $html .= "</div>";

        return $html;
    }

    public static function isMobile()
    {
        $app = JFactory::getApplication();
        $mobile_detect = new Mobile_Detect();
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
        CRMText::script('COBALT_PLEASE_SELECT_A_USER');
        CRMText::script('COBALT_SUCCESS_MESSAGE');
        CRMText::script('COBALT_ADD_NEW_NOTE');
        CRMText::script('COBALT_ADD');
        CRMText::script('COBALT_SUCCESS_MESSAGE');
        CRMText::script('COBALT_GENERIC_UPDATED');
        CRMText::script('COBALT_DEALS_BY_STAGE');
        CRMText::script('COBALT_TOTAL');
        CRMText::script('COBALT_DEALS_BY_STATUS');
        CRMText::script('COBALT_REVENUE_FROM_LEAD_SOURCES');
        CRMText::script('COBALT_CURRENCY');
        CRMText::script('COBALT_YEARLY_COMMISSIONS');
        CRMText::script('COBALT_MONTHLY_COMMISSIONS');
        CRMText::script('COBALT_YEARLY_REVENUE');
        CRMText::script('COBALT_MONTHLY_REVENUE');
        CRMText::script('COBALT_WEEK');
        CRMText::script('COBALT_DELETE_GOALS');
        CRMText::script('COBALT_DELETE_GOAL_CONFIRMATION');
        CRMText::script('COBALT_TODAY');
        CRMText::script('COBALT_MONTH');
        CRMText::script('COBALT_WEEK');
        CRMText::script('COBALT_DAY');
        CRMText::script('COBALT_DAYS');
        CRMText::script('COBALT_EDIT_NOTE');
        CRMText::script('COBALT_EDIT_NOTES');
        CRMText::script('COBALT_JANUARY');
        CRMText::script('COBALT_FEBRUARY');
        CRMText::script('COBALT_MARCH');
        CRMText::script('COBALT_APRIL');
        CRMText::script('COBALT_MAY');
        CRMText::script('COBALT_JUNE');
        CRMText::script('COBALT_JULY');
        CRMText::script('COBALT_AUGUST');
        CRMText::script('COBALT_SEPTEMBER');
        CRMText::script('COBALT_OCTOBER');
        CRMText::script('COBALT_NOVEMBER');
        CRMText::script('COBALT_DECEMBER');
        CRMText::script('COBALT_JAN');
        CRMText::script('COBALT_FEB');
        CRMText::script('COBALT_MAR');
        CRMText::script('COBALT_APR');
        CRMText::script('COBALT_MAY');
        CRMText::script('COBALT_JUN');
        CRMText::script('COBALT_JUL');
        CRMText::script('COBALT_AUG');
        CRMText::script('COBALT_SEP');
        CRMText::script('COBALT_OCT');
        CRMText::script('COBALT_NOV');
        CRMText::script('COBALT_DEC');
        CRMText::script('COBALT_SUNDAY');
        CRMText::script('COBALT_MONDAY');
        CRMText::script('COBALT_TUESDAY');
        CRMText::script('COBALT_WEDNESDAY');
        CRMText::script('COBALT_THURSDAY');
        CRMText::script('COBALT_FRIDAY');
        CRMText::script('COBALT_SATURDAY');
        CRMText::script('COBALT_SUN');
        CRMText::script('COBALT_MON');
        CRMText::script('COBALT_TUE');
        CRMText::script('COBALT_WED');
        CRMText::script('COBALT_THU');
        CRMText::script('COBALT_FRI');
        CRMText::script('COBALT_SAT');
        CRMText::script('COBALT_EDIT');
        CRMText::script('COBALT_DELETE_CONFIRMATION');
        CRMText::script('COBALT_DELETE_PERSON_FROM_DEAL_CONFIRM');
        CRMText::script('COBALT_MARK_COMPLETE');
        CRMText::script('COBALT_MARK_INCOMPLETE');
        CRMText::script('COBALT_SUCCESSFULLY_REMOVED_EVENT');
        CRMText::script('COBALT_ITEM_SUCCESSFULLY_UPDATED');
        CRMText::script('COBALT_OK');
        CRMText::script('COBALT_ADDED');
        CRMText::script('COBALT_VERIFY_ALERT');
        CRMText::script('COM_CMERY_ARE_YOU_SURE_DELETE_REPORT');
        CRMText::script('COBALT_DEALS_BY_STAGE');
        CRMText::script('COBALT_DEALS_BY_STATUS');
        CRMText::script('COBALT_TOTAL');
        CRMText::script('COBALT_UPDATE');
        CRMText::script('COBALT_UPLOADING');
        CRMText::script('COBALT_YOUR_DOCUMENT_IS_BEING_UPLOADED');
        CRMText::script('COBALT_MESSAGE_DETAILS');
        CRMText::script('COBALT_CLICK_TO_EDIT');
        CRMText::script('COBALT_UPDATED_PRIMARY_CONTACT');
        CRMText::script('COBALT_MANAGE_MAILING_LISTS');
        CRMText::script('COBALT_MAILING_LIST_LINKS');
        CRMText::script('COBALT_REMOVE');
        CRMText::script('COBALT_ADD');
        CRMText::script('COBALT_EMAIL');
        CRMText::script('COBALT_ACTIVE_DEAL');
        CRMText::script('COBALT_START_TYPING_DEAL');
        CRMText::script('COBALT_START_TYPING_PERSON');
        CRMText::script('COBALT_SUCCESSFULLY_SHARED_ITEM');
        CRMText::script('COBALT_SUCCESSFULLY_UNSHARED_ITEM');
        CRMText::script('COBALT_SHARE_ITEM');
        CRMText::script('COBALT_STAGE');
        CRMText::script('COBALT_EDITING_EVENT');
        CRMText::script('COBALT_EDITING_TASK');
        CRMText::script('COBALT_ADDING_EVENT');
        CRMText::script('COBALT_ADDING_TASK');
        CRMText::script('COBALT_CONTACTS');
    }

    public static function getListEditActions()
    {
        $list_html  = "";

        $list_html .= "<div id='list_edit_actions'>";
        $list_html .= '<ul class="inline-list">';
        $list_html .= '<li>'.CRMText::_('COBALT_PERFORM').'</li>';
        $list_html .= '<li class="dropdown">';
        $list_html .= '<a class="dropdown-toggle" href="#" data-toggle="dropdown" role="button" >'.CRMText::_('COBALT_ACTIONS').'</a>';
            $list_html .= '<ul class="dropdown-menu" role="menu" aria-labelledby="">';
                if ( CobaltHelperUsers::canDelete() ) {
                    $list_html .= '<li><a onclick="deleteListItems()">'.CRMText::_('COBALT_DELETE').'</a></li>';
                }
            $list_html .= '</ul>';
        $list_html .= '</li>';
        $list_html .= '<li>'.CRMText::_('COBALT_ON_THE')."<span id='items_checked'></span> ".CRMText::_('COBALT_ITEMS').'</li>';
        $list_html .= '</ul>';
        $list_html .= "</div>";

        return $list_html;
    }

    public static function getEventListEditActions()
    {
        $list_html  = "";

        $list_html .= "<div id='list_edit_actions'>";
        $list_html .= CRMText::_('COBALT_PERFORM')."<a class='dropdown' id='list_edit_actions_dropdown_link'>".CRMText::_('COBALT_ACTIONS')."</a>".CRMText::_('COBALT_ON_THE')."<span id='items_checked'></span> ".CRMText::_('COBALT_ITEMS');
        $list_html .= '<div class="filters" id="list_edit_actions_dropdown">';
        $list_html .= '<ul>';
        $list_html .= '<li><a onclick="deleteListItems()">'.CRMText::_('COBALT_DELETE').'</a></li>';
        $list_html .= '</ul>';
        $list_html .= "</div>";
        $list_html .= "</div>";

        return $list_html;
    }

 }
