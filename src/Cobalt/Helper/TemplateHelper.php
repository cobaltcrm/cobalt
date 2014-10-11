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

            $app->clearMessageQueue();
        }

	    return $js;
    }
}
