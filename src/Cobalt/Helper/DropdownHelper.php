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

use JFactory;
use JHtml;
use Cobalt\Model\People as PeopleModel;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class DropdownHelper
{

    public static function generateDropdown($type, $selection = null, $name = null, $raw = false)
    {
        //base html
        $html = '';

        //grab db
        $db = \Cobalt\Container::fetch('db');

        //generate query based on type
        $query = $db->getQuery(true);

        switch ($type) {
            case "company":
                $query->select('id,name FROM #__companies AS c where c.published > 0');
                break;
            case "stage":
                $query->select('id,name FROM #__stages');
                break;
            case "source":
                $query->select('id,name FROM #__sources');
                break;
            case "deal_status":
                $query->select('id,name FROM #__deal_status');
                break;
            case "people_status":
                $query->select('id,name FROM #__people_status');
                break;
            case "deal":
                $query->select('d.id,d.name');
                $query->from("#__deals AS d");
                $query->where("d.published > 0");
                $query->leftJoin('#__users AS users ON users.id = d.owner_id');
                /** ---------------------------------------------------------------
                 * Filter data using member role permissions
                 */
                $member_id = UsersHelper::getUserId();
                $member_role = UsersHelper::getRole();
                $team_id = UsersHelper::getTeamId();
                if ($member_role != 'exec') {
                     //manager filter
                    if ($member_role == 'manager') {
                        $query->where('users.team_id = '.$team_id);
                    } else {
                    //basic user filter
                        $query->where(array('d.owner_id = '.$member_id));
                    }
                }
            break;
        }

        //run query and grab results
        if ($query!="") {
            $db->setQuery($query);
            $row = $db->loadAssocList();
        }

        if ($type == 'owner')
        {
            $me = array(array('label' => TextHelper::_('COBALT_ME'), 'value' => UsersHelper::getLoggedInUser()->id));
            $users = UsersHelper::getUsers(null, true);
            $row = array_merge($me, $users);
        }

        if ( !isset($row) ) {
            $row = array();
        } elseif ( !is_array($row) && !(count($row) > 0)) {
            $row = array();
        }

        if ($raw) {
            return $row;
        }

        //determine which kind of dropdown we are generating
        $selected = ( $selection == null ) ? "selected='selected'" : '';
         switch ($type) {
            case "company":
                $name = $name ? $name : "name=company_id";
                $html = '
                    <select class="inputbox form-control" '.$name.' id="company_id">';
                        $html .= "<option value='0' ".$selected.">" . TextHelper::_('COBALT_DROPDOWN_COMPANY_SELECT_COMPANY_OPTION_LABEL');
                        foreach ($row as $company => $info) {
                            $selected = ( $info['id'] == $selection ) ? "selected='selected'" : '';
                            $html .= '<option value="'.$info['id'].'" '.$selected.' >'.$info['name'].'</option>';
                        }
                $html .= '</select>';
                break;
            case "stage":
                $name = $name ? $name : "name=stage_id";
                $html = '
                     <select class="inputbox form-control" '.$name.' id="stage_id">';
                        $html .= "<option value='0' ".$selected.">" . TextHelper::_('COBALT_DROPDOWN_STAGE_SELECT_STAGE_OPTION_LABEL');
                        foreach ($row as $stage => $info) {
                             $selected = ( $info['id'] == $selection ) ? "selected='selected'" : '';
                            $html .= '<option value="'.$info['id'].'" '.$selected.' '.$name.' >'.$info['name'].'</option>';
                        }

                    $html .='</select>';
                break;
            case "source":
                $name = $name ? $name : "name=source_id";
                $html = '<select class="inputbox form-control" '.$name.' id="source_id">';
                        $html .= "<option value='0' ".$selected.">" . TextHelper::_('COBALT_DROPDOWN_SOURCE_SELECT_SOURCE_OPTION_LABEL');
                        if (count($row) > 0) {
                            foreach ($row as $source => $info) {
                                 $selected = ( $info['id'] == $selection ) ? "selected='selected'" : '';
                                $html .= '<option value="'.$info['id'].'" '.$selected.' '.$name.' >'.$info['name'].'</option>';
                            }
                        }
                    $html .='</select>';
                break;
            case "probability":
                $name = $name ? $name : "name=probability";
                $html = '
                    <select class="inputbox form-control" '.$name.' id="probability_id">';
                        $html .= "<option value='0' ".$selected.">" . TextHelper::_('COBALT_DROPDOWN_PROBABILITY_SELECT_PROBABILITY_OPTION_LABEL');
                        for ($i=5; $i<=95; $i+=5) {
                                $selected = ( $i == $selection ) ? "selected='selected'" : '';
                                $html .= '<option value="'.$i.'" '.$selected.' '.$name.' >'.$i.'%</option>';
                            }
                $html .= '</select>';
                break;
            case "deal_status":
                $name = $name ? $name : "name=status_id";
                $html = '
                    <select class="inputbox form-control" '.$name.' id="status_id">';
                    $html .= "<option value='0' ".$selected.">" . TextHelper::_('COBALT_DROPDOWN_DEAL_STATUS_SELECT_STATUS_OPTION_LABEL');
                        foreach ($row as $status => $info) {
                             $selected = ( $info['id'] == $selection ) ? "selected='selected'" : '';
                            $html .= '<option value="'.$info['id'].'" '.$selected.' '.$name.' >'.$info['name'].'</option>';
                        }

                    $html .='</select>';
                break;
            case "people_status":
                $name = $name ? $name : "name=status_id";
                $html = '
                    <select class="inputbox form-control" '.$name.' id="status_id">';
                    $html .= "<option value='0' ".$selected.">" . TextHelper::_('COBALT_DROPDOWN_PEOPLE_STATUS_SELECT_STATUS_OPTION_LABEL');
                        foreach ($row as $status => $info) {
                             $selected = ( $info['id'] == $selection ) ? "selected='selected'" : '';
                            $html .= '<option value="'.$info['id'].'" '.$selected.' '.$name.' >'.$info['name'].'</option>';
                        }

                    $html .='</select>';
                break;
            case "deal":
                $name = $name ? $name : "name=deal_id";
                $html = '
                    <select class="inputbox form-control" '.$name.' id="deal_id">';
                    $html .= "<option value='0' ".$selected.">" . TextHelper::_('COBALT_DROPDOWN_DEAL_SELECT_DEAL_OPTION_LABEL');
                        foreach ($row as $deal => $info) {
                             $selected = ( $info['id'] == $selection ) ? "selected='selected'" : '';
                            $html .= '<option value="'.$info['id'].'" '.$selected.' '.$name.' >'.$info['name'].'</option>';
                        }

                    $html .='</select>';
                break;
            case "owner":
                $name = $name ? $name : "name=owner_id";
                $html = '
                    <select class="inputbox form-control" '.$name.' id="owner_id">';
                    $html .= "<option value=\"0\" ".$selected.">" . TextHelper::_('COBALT_DROPDOWN_OWNER_SELECT_OWNER_OPTION_LABEL');
                        foreach ($row as $i => $info) {
                             $selected = ( $info['value'] == $selection ) ? "selected='selected'" : '';
                            $html .= '<option value="'.$info['value'].'" '.$selected.' '.$name.' >'.$info['label'].'</option>';
                        }

                    $html .='</select>';
                break;

            default:

                $model = DropdownHelper::getModelFromType($type);

                $html 	 = '<ul>';
                $html 	.= '<li><a href="javascript:void(0)" onclick="saveAjax(\''.$type.'\',\''.$model.'\',\'Lead\')">'.TextHelper::_('COBALT_PERSON_LEAD').'</a></li>';
                $html 	.= '<li><a href="javascript:void(0)" onclick="saveAjax(\''.$type.'\',\''.$model.'\',\'Contact\')">'.TextHelper::_('COBALT_PEOPLE_CONTACT').'</a></li>';
                $html 	.= "</ul>";

                break;
        }

        return $html;

     }

     public static function getModelFromType($type)
     {
         if (stripos($type,'person')!==false) {
             $model = 'people';
         }

         return $model;

     }

    public static function generateCustom($type,$id=null)
    {
             //base html
             $return = array();

            //grab db
            $db = \Cobalt\Container::fetch('db');

            //generate query based on type
             $query = $db->getQuery(true);
            //determine specific entry to generate
            $query->select("cf.* FROM #__".$type."_custom AS cf");

            $query->order("cf.ordering");

            //set query
            $db->setQuery($query);
            $row = $db->loadAssocList();

            //determine selected values
            if ($id) {
                $custom_data = self::getCustomData($id,$type);
            }

            if ( is_array($row) && count($row) > 0 ) {

                //loop for explosion delims
                foreach ($row as $custom) {

                    //retrieve custom values
                    $custom['values'] = json_decode($custom['values']);

                    //determine selected values
                    if ($id && $custom['type'] != 'forecast') {
                        $custom['selected'] = ( array_key_exists($custom['id'],$custom_data) ) ? $custom_data[$custom['id']] : TextHelper::_('COBALT_CLICK_TO_EDIT');
                    }
                    //append items to array
                    $return[] = $custom;
                }
            }

            //return
            return $return;

        }

        //get custom data to prefill dropdowns
        public static function getCustomData($id,$type)
        {
            //get dbo
            $db = \Cobalt\Container::fetch('db');
            $query = $db->getQuery(true);

            //query
            $query->select("* FROM #__".$type."_custom_cf");
            $query->where($type."_id=$id");

            //return results
            $db->setQuery($query);
            $db_results = $db->loadAssocList();

            $results = array();
            if ( count($db_results) != 0 ) {
                foreach ($db_results as $key => $row) {
                    $results[$row['custom_field_id']] = $row['value'];
                }
            }

            return $results;

        }

        /**
         * Get custom field values from picklists // forecasts // otherwise return the value as it was an input field
         */
        public static function getCustomValue($customType,$customNameOrId,$customValue,$itemId)
        {
            $db = \Cobalt\Container::fetch('db');
            $query = $db->getQuery(true);

            $id = str_replace("custom_","",$customNameOrId);

            $query->select("c.type,c.values")
                ->from("#__".$customType."_custom AS c")
                ->where("c.id=".$id);

            $db->setQuery($query);

            $custom = $db->loadObject();

            switch ($custom->type) {
                case "forecast":
                    $query->clear();
                    $query->select("(d.amount * ( d.probability / 100 )) AS amount")
                        ->from("#__deals AS d")
                        ->where("d.id=".$itemId);
                    $db->setQuery($query);
                    $result = $db->loadResult();

                    return ConfigHelper::getCurrency().$result;
                break;
                case "currency":
                    return ConfigHelper::getCurrency().$customValue;
                break;
                case "picklist":
                    $values = json_decode($custom->values);

                    return array_key_exists($customValue,$values) ? $values[$customValue] : TextHelper::_('COBALT_NONE');
                break;
                case "date":
                    return DateHelper::formatDate($customValue);
                break;
                default:
                    return $customValue;
                break;
            }

            return $customValue;
        }

        /**
         * Get Leaderboards
         * @param none
         * @return mixed $list goals with leaderboards matched
         */
        public static function getLeaderBoards()
        {
            //load database
            $db = \Cobalt\Container::fetch('db');
            $query = $db->getQuery(true);

            //load goals associate with user depending on team//role that have a leaderboard flag in the database
            $query->select("g.*")->from("#__goals AS g")->where("g.leaderboard=1");

            //return goals
            $db->setQuery($query);
            $results = $db->loadAssocList();

            //generate dropdown list array
            $list = array();

            if (count($results) > 0) {
                foreach ($results as $key=>$goal) {
                    $list[$goal['id']] = $goal['name'];
                }
            }

            //return results
            return $list;
        }

        /**
         * Get team names for dropdowns
         * @param mixed $return array of team names for dropdown
         */
        public static function getTeamNames()
        {
            //get all teams
            $teams = UsersHelper::getTeams();

            //generate array
            $return = array();
            $managerTeam = UsersHelper::getTeamId();
            if ( is_array($teams) && count($teams) > 0 ) {
                foreach ($teams as $key=>$value) {
                    $return[$value['team_id']] = $value['team_name'] . TextHelper::_('COBALT_TEAM_APPEND');
                }
            }
            unset($return[$managerTeam]);

            //return array
            return $return;

        }

        /**
         * Get user names for dropdowns
         * @param mixed $return array of user names for dropdown
         */
        public static function getUserNames()
        {
            //get all teams
            $users = UsersHelper::getUsers();

            //generate array
            $return = array();
            if ( is_array($users) && count($users) > 0 ) {
                foreach ($users as $key=>$value) {
                    $return[$value['id']] = $value['first_name'] . ' ' . $value['last_name'];
                }
            }

            //return array
            return $return;

        }

        /**
         * Get person contact types
         * @param  [type] $contact_types [description]
         * @return [type]                [description]
         */

        public static function getContactTypes($contact_type_name=null)
        {
            $contact_types = array('contact' => TextHelper::_('COBALT_CONTACT'), 'lead' => TextHelper::_('COBALT_LEAD'));

            if ( !in_array($contact_type_name,$contact_types) ) {
                $currentValue = '0'; //Set this value from DB, etc.
                $arr = array();
                foreach ($contact_types as $name => $value) {
                  $arr[] = JHtml::_('select.option', $name, $value);
                }

                return JHtml::_('select.genericlist', $arr, 'type', 'class="inputbox"', 'value', 'text', $currentValue);
            } else {
                return $contact_type_name;
            }

        }

        public static function getPeopleList()
        {
            //open model
            $model = new PeopleModel;
            //retrieve all people
            $people = $model->getPeopleList();
            $people_list = array();
            if ( count($people) ) {
                foreach ($people as $index => $row) {
                    $people_list[$row['id']] = $row['first_name'].' '.$row['last_name'];
                }
            }

            return $people_list;
        }

        //load the navigation menu
        public static function getMemberRoles()
        {
            return array(   'exec'=>'Executive',
                            'manager'=>'Manager',
                            'basic'=>'Basic'    );
        }

        //load teams to assign to users
        public static function getTeams($team=null)
        {
            //get database
            $db = \Cobalt\Container::fetch('db');
            $query = $db->getQuery(true);
            //query string
            //u.id//
            $query->select("t.team_id AS id,u.first_name,u.last_name,u.team_id,IF(t.name!='',t.name,CONCAT(u.first_name,' ',u.last_name)) AS team_name");
            $query->from("#__users AS u");
            $query->leftJoin("#__teams AS t on t.leader_id = u.id");
            $query->where("u.role_type='manager'");
            //get results
            $db->setQuery($query);
            $results = $db->loadAssocList();
            //generate users object
            $users = array();
            if ( is_array($results) && count($results) > 0 ) {
                foreach ($results as $key=>$user) {
                    $users[$user['id']] = $user['team_name'];
                }
            }
            if ($team > 0) {
                // unset($users[$team]);
            }
            //return
            return $users;
        }

        public static function getManagers($remove=null)
        {
            //get database
            $db = \Cobalt\Container::fetch('db');
            $query = $db->getQuery(true);
            //query string
            $query->select("u.id,u.first_name,u.last_name,u.team_id");
            $query->from("#__users AS u");
            $query->where("u.role_type='manager'");
            //get results
            $db->setQuery($query);
            $results = $db->loadAssocList();
            //generate users object
            $users = array();
            foreach ($results as $key=>$user) {
                if ($user['id'] == $remove) {
                    unset($results[$key]);
                } else {
                    $users[$user['id']] = $user['first_name'] . ' ' . $user['last_name'];
                }
            }
            //return
            return $users;
        }

        public static function getSources()
        {
            return array(  'per' => 'Per Lead/Deal',
                            'flat'=> 'Flat Fee'
                            );
        }

        public static function getCustomTypes($type)
        {
            switch ($type) {
                case "deal":
                    $arr = array(  'number'    =>  TextHelper::_('COBALT_NUMBER'),
                                    'text'      =>  TextHelper::_('COBALT_ADMIN_GENERIC_TEXT'),
                                    'currency'  =>  TextHelper::_('COBALT_CURRENCY'),
                                    'picklist'  =>  TextHelper::_('COBALT_PICKLIST'),
                                    'forecast'  =>  TextHelper::_('COBALT_FORECAST'),
                                    'date'      =>  TextHelper::_('COBALT_DATE')  );
                break;
                case "company":
                    $arr = array(  'number'    =>  TextHelper::_('COBALT_NUMBER'),
                                    'text'      =>  TextHelper::_('COBALT_ADMIN_GENERIC_TEXT'),
                                    'currency'  =>  TextHelper::_('COBALT_CURRENCY'),
                                    'picklist'  =>  TextHelper::_('COBALT_PICKLIST'),
                                    'date'      =>  TextHelper::_('COBALT_DATE')  );
                break;
                case "people":
                    $arr = array(  'number'    =>  TextHelper::_('COBALT_NUMBER'),
                                    'text'      =>  TextHelper::_('COBALT_ADMIN_GENERIC_TEXT'),
                                    'currency'  =>  TextHelper::_('COBALT_CURRENCY'),
                                    'picklist'  =>  TextHelper::_('COBALT_PICKLIST'),
                                    'date'      =>  TextHelper::_('COBALT_DATE')  );
                break;
            }

            return $arr;
        }

        public static function getTemplateTypes()
        {
            return array(  'milestone'     =>  TextHelper::_('COBALT_MILESTONE'),
                            'call'          =>  TextHelper::_('COBALT_CALL'),
                            'appointment'   =>  TextHelper::_('COBALT_APPOINTMENT'),
                            'email'         =>  TextHelper::_('COBALT_USERS_HEADER_EMAIL'),
                            'todo'          =>  TextHelper::_('COBALT_TODO'),
                            'fax'           =>  TextHelper::_('COBALT_FAX')   );
        }

        public static function showImportTypes($selected="deals",$name="import_type",$class='class="inputbox"')
        {
            $import_types = array(
                'deals'=>TextHelper::_('COBALT_DEALS'),
                'people'=>TextHelper::_('COBALT_PEOPLE'),
                'companies'=>TextHelper::_('COBALT_COMPANIES')
                );

            $arr = array();
            foreach ($import_types as $value => $label) {
              $arr[] = JHtml::_('select.option', $value, $label);
            }

            return JHtml::_('select.genericlist', $arr, $name, $class, 'value', 'text', $selected);

        }

        public static function getFormTypes($selected="lead",$name="type",$class="class='form-control'")
        {
            $import_types = array(
                'lead'=>TextHelper::_('COBALT_LEAD'),
                'contact'=>TextHelper::_('COBALT_CONTACT')
                // 'company'=>JText::_('COBALT_COMPANY')
                // 'deal'=>JText::_('COBALT_DEAL')
                );

            $arr = array();
            foreach ($import_types as $value => $label) {
              $arr[] = JHtml::_('select.option', $value, $label);
            }

            return JHtml::_('select.genericlist', $arr, $name, $class, 'value', 'text', $selected);

         }

         public static function getFormFields($type)
         {
             $arr = array();

             switch ($type) {
                 case "people":
                     $base = array(
                             array('display'=>ucwords(TextHelper::_('COBALT_PERSON_FIRST')),'name'=>'first_name','type'=>'text'),
                             array('display'=>ucwords(TextHelper::_('COBALT_PERSON_LAST')),'name'=>'last_name','type'=>'text'),
                             array('display'=>ucwords(TextHelper::_('COBALT_PERSON_COMPANY')),'name'=>'company_name','type'=>'text'),
                             array('display'=>ucwords(TextHelper::_('COBALT_PERSON_POSITION')),'name'=>'position','type'=>'text'),
                             array('display'=>ucwords(TextHelper::_('COBALT_PERSON_PHONE')),'name'=>'phone','type'=>'text'),
                             array('display'=>ucwords(TextHelper::_('COBALT_PERSON_EMAIL')),'name'=>'email','type'=>'text'),
                             array('display'=>ucwords(TextHelper::_('COBALT_PERSON_SOURCE')),'name'=>'source_name','type'=>'text'),
                             array('display'=>ucwords(TextHelper::_('COBALT_HOME_ADDRESS_1_NULL')),'name'=>'home_address_1','type'=>'text'),
                             array('display'=>ucwords(TextHelper::_('COBALT_HOME_ADDRESS_2_NULL')),'name'=>'home_address_2','type'=>'text'),
                             array('display'=>ucwords(TextHelper::_('COBALT_HOME_CITY_NULL')),'name'=>'home_city','type'=>'text'),
                             array('display'=>ucwords(TextHelper::_('COBALT_HOME_STATE_NULL')),'name'=>'home_state','type'=>'text'),
                             array('display'=>ucwords(TextHelper::_('COBALT_HOME_ZIP_NULL')),'name'=>'home_zip','type'=>'text'),
                             array('display'=>ucwords(TextHelper::_('COBALT_HOME_COUNTRY_NULL')),'name'=>'home_country','type'=>'text'),
                             array('display'=>ucwords(TextHelper::_('COBALT_PERSON_FAX')),'name'=>'fax','type'=>'text'),
                             array('display'=>ucwords(TextHelper::_('COBALT_PERSON_WEBSITE')),'name'=>'website','type'=>'text'),
                             array('display'=>ucwords(TextHelper::_('COBALT_PERSON_FACEBOOK_URL')),'name'=>'facebook_url','type'=>'text'),
                             array('display'=>ucwords(TextHelper::_('COBALT_PERSON_TWITTER_USER')),'name'=>'twitter_user','type'=>'text'),
                             array('display'=>ucwords(TextHelper::_('COBALT_PERSON_LINKEDIN_URL')),'name'=>'linkedin_url','type'=>'text'),
                             array('display'=>ucwords(TextHelper::_('COBALT_PERSON_STATUS')),'name'=>'status_name','type'=>'text'),
                             array('display'=>ucwords(TextHelper::_('COBALT_WORK_ADDRESS_1_NULL')),'name'=>'work_address_1','type'=>'text'),
                             array('display'=>ucwords(TextHelper::_('COBALT_WORK_ADDRESS_2_NULL')),'name'=>'work_address_2','type'=>'text'),
                             array('display'=>ucwords(TextHelper::_('COBALT_WORK_CITY_NULL')),'name'=>'work_city','type'=>'text'),
                             array('display'=>ucwords(TextHelper::_('COBALT_WORK_STATE_NULL')),'name'=>'work_state','type'=>'text'),
                             array('display'=>ucwords(TextHelper::_('COBALT_WORK_ZIP_NULL')),'name'=>'work_zip','type'=>'text'),
                             array('display'=>ucwords(TextHelper::_('COBALT_WORK_COUNTRY_NULL')),'name'=>'work_country','type'=>'text'),
                             array('display'=>ucwords(TextHelper::_('COBALT_PERSON_MOBILE_PHONE')),'name'=>'mobile_phone','type'=>'text'),
                             array('display'=>ucwords(TextHelper::_('COBALT_PERSON_HOME_EMAIL')),'name'=>'home_email','type'=>'text'),
                             array('display'=>ucwords(TextHelper::_('COBALT_PERSON_OTHER_EMAIL')),'name'=>'other_email','type'=>'text'),
                             array('display'=>ucwords(TextHelper::_('COBALT_PERSON_HOME_PHONE')),'name'=>'home_phone','type'=>'text'),
                         );
                    $custom = self::generateCustom('people');
                    if ( count($custom) > 0 ) {
                        $custom = self::cleanCustomForm($custom);
                    }
                    $arr = array_merge($base,$custom);
                 break;
                 case "deal":
                     $base = array(

                         );
                 break;
                 case "company":
                     $base = array(

                         );
                 break;
             }

             return $arr;
         }

         public static function cleanCustomForm($data)
         {
             if ( count($data) > 0 ) {
                 foreach ($data as $key => $field) {
                     if ($field['type'] == "date" || $field['type'] == "forecast") {
                         unset($data[$key]);
                     } else {
                         $data[$key]['display'] = $field['name'];
                         $data[$key]['name'] = "custom_".$field['id'];
                     }
                 }
             }

             return $data;
         }

         public static function generateDealStatuses($selected=null, $name="status_id", $class="class='inputbox form-control'")
         {
            $db = \Cobalt\Container::fetch('db');
            $query = $db->getQuery(true);
            $query->select("id,name")->from("#__deal_status");

            $db->setQuery($query);

            $statuses = $db->loadAssocList();

            $options = array();
            $options[0] = TextHelper::_('COBALT_NONE_STATUS');
            if ( count ($statuses) >  0 ) {
                foreach ($statuses as $status) {
                    $options[$status['id']] = TextHelper::_('COBALT_'.strtoupper($status['name'])."_STATUS");
                }
            }

            $arr = array();
            foreach ($options as $value => $label) {
              $arr[] = JHtml::_('select.option', $value, $label);
            }

            return JHtml::_('select.genericlist', $arr, $name, $class, 'value', 'text', $selected);

         }

 }
