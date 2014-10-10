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

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

 class CobaltHelper
 {
    public static function percent2Color($value, $brightness = 255, $max = 100, $min = 0, $thirdColorHex = '00')
    {
        // Calculate first and second color (Inverse relationship)
        $value = number_format($value);
        $first = (1 - ($value / $max)) * $brightness;
        $second = ($value / $max) * $brightness;

        // Find the influence of the middle color (yellow if 1st and 2nd are red and green)
        $diff = abs($first - $second);
        $influence = ($brightness - $diff) / 2;
        $first = intval($first + $influence);
        $second = intval($second + $influence);

        // Convert to HEX, format and return
        $firstHex = str_pad(dechex($first), 2, 0, STR_PAD_LEFT);
        $secondHex = str_pad(dechex($second), 2, 0, STR_PAD_LEFT);

        return $firstHex . $secondHex . $thirdColorHex ;
    }

    /**
     * Get task and event templates
     * @param  [String] $type ["deal","person"]
     * @param  [int]    $id   [Optional ID to get all events with a template]
     * @return [mixed]  $results
     */
    public static function getTaskTemplates($type, $id = null)
    {
	    /** @var \Joomla\Database\DatabaseDriver $db */
        $db = \Cobalt\Container::fetch('db');
        $query = $db->getQuery(true);
        $query->select("t.*")->from("#__templates AS t")->where("t.type=".$db->quote($type));
        $db->setQuery($query);

        return $db->loadAssocList();
    }

    public static function getGravatar($email,$size = null,$image = false, $default=null)
    {
        //Default icon size
        if (!$size) { $size = 50; }

        $url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?d=" . urlencode( $default ) . "&s=" . $size;

        if ($image) {
            $url = '<img src="'.$url.'" />';
        }

        return $url;
    }

    /**
     * Method to store custom field cf data associated with items
     * @param  int   $id      : The id of the item we wish to store associated data
     * @param  mixed $cf_data : The data to be stored
     * @return void
     *
     */
    public static function storeCustomCf($id,$cf_data,$type)
    {
        //Get DBO
	    /** @var \Joomla\Database\DatabaseDriver $db */
        $db = \Cobalt\Container::fetch('db');
        $query = $db->getQuery(true);

        //date generation
        $date = DateHelper::formatDBDate(date('Y-m-d H:i:s'));

        //Loop through $cf_data array to update/insert
        for ( $i=0; $i<count($cf_data); $i++ ) {
            //assign the data
            $row = $cf_data[$i];

            $query->select("COUNT(*)")
                    ->from("#__".$type."_custom_cf")
                    ->where($type."_id=".$id." AND custom_field_id=".$row['custom_field_id']);

            $db->setQuery($query);
            $count = $db->loadResult();

            if ($count > 0) {
                //mysql query
                $query->clear();
                $query->update('#__'.$type.'_custom_cf');
                $query->set($type."_id=".$id.
                             ",custom_field_id=".$row['custom_field_id'].
                             ",value=".$db->quote($row['custom_field_value']).
                             ",modified=".$db->quote($date));
                $query->where($type."_id=$id AND custom_field_id=".$row['custom_field_id']);
                $db->setQuery($query);
                $db->execute();
            } else {
                $query->clear();
                $query->insert('#__'.$type.'_custom_cf');
	            $query->columns(array($type."_id", 'custom_field_id', 'value', 'modified'));
	            $query->values($id . ', ' . $row['custom_field_id'] . ', ' . $db->quote($row['custom_field_value']) . ', ' . $db->quote($date));
                $db->setQuery($query);
                $db->execute();
            }

        }

    }

    public static function checkEmailName($email)
    {
        $db = \Cobalt\Container::fetch('db');
        $query = $db->getQuery(TRUE);

        $query->select("email")
            ->from("#__users_email_cf")
            ->where("email=" . $db->quote($email));

        $db->setQuery($query);

        $results = $db->loadObjectList();

        if ( count($results) > 0 ) {
            return TRUE;

        } else {

            $query->clear()
                    ->select("j.email")
                    ->from("#__users AS u")
                    ->leftJoin("#__users AS j ON j.id = u.id")
                    ->where("j.email=" . $db->quote($email));

            $db->setQuery($query);

            $results = $db->loadObjectList();

            if ( count($results) > 0 ) {
                return TRUE;

            }

        }

        return FALSE;
    }

    public static function getBytes($val)
    {
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);
        switch ($last) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }

        return $val;
    }

    public static function shareItem($itemId=null,$itemType=null,$userId=null)
    {
        $app = \Cobalt\Container::fetch('app');

        $itemId = $itemId ? $itemId : $app->input->get('item_id');
        $itemType = $itemType ? $itemType : $app->input->get('item_type');
        $userId = $userId ? $userId : $app->input->get('user_id');

        $db = \Cobalt\Container::fetch('db');
        $query = $db->getQuery(true);

        $query->insert("#__shared")
            ->columns('item_id,item_type,user_id')
            ->values($itemId.",".$db->quote($itemType).",".$userId);

        $db->setQuery($query);
        $db->execute();

        return true;
    }

    public static function unshareItem($itemId=null,$itemType=null,$userId=null)
    {
        $app = \Cobalt\Container::fetch('app');

        $itemId = $itemId ? $itemId : $app->input->get('item_id');
        $itemType = $itemType ? $itemType : $app->input->get('item_type');
        $userId = $userId ? $userId : $app->input->get('user_id');

        $db = \Cobalt\Container::fetch('db');
        $query = $db->getQuery(true);

        $query->delete("#__shared")
            ->where('item_id='.$itemId)
            ->where('item_type='.$db->quote($itemType))
            ->where('user_id='.$userId);

        $db->setQuery($query);
        $db->execute();

        return true;
    }

    public static function showShareDialog()
    {
        $app = \Cobalt\Container::fetch('app');

        $document = $app->getDocument();
        $document->addScriptDeclaration('var users='.json_encode(UsersHelper::getAllSharedUsers()).';');

        $html = "<div class='modal hide fade' role='dialog' tabindex='-1' aria-hidden='true' id='share_item_dialog'>";
        $html .= '<div class="modal-header small"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h3>'.TextHelper::_('COBALT_SHARING_ITEM').'</h3></div>';
        $html .= '<div class="modal-body">';
        $html .= '<div>'.TextHelper::_('COBALT_SHARE_DESC').'</div>';
        $html .= '<div class="input-append">';
        $html .= '<input id="shared_user_name" class="inputbox" type="text" placeholder="'.TextHelper::_('COBALT_BEGIN_TYPING_USER').'" />';
        $html .= '<input type="hidden" name="shared_user_id" id="shared_user_id" />';
        $html .= '<a class="btn btn-success" href="javascript:void(0);" onclick="shareItem();"><i class="glyphicon glyphicon-plus icon-white"></i>'.TextHelper::_('COBALT_ADD').'</a>';
        $html .= '</div>';
        $html .= '<div id="shared_user_list">';

        $itemId = $app->input->get('id');
        $itemType = $app->input->get('layout');

        $users = UsersHelper::getItemSharedUsers($itemId,$itemType);
        if ( count ( $users ) > 0 ) {
            foreach ($users as $user) {
                $html .= '<div id="shared_user_'.$user->value.'"><i class="glyphicon glyphicon-user"></i>'.$user->label." - <a class='btn btn-danger btn-mini' href='javascript:void(0);' onclick='unshareItem(".$user->value.");'>".TextHelper::_('COBALT_REMOVE')."</a></div>";
            }
        }

        $html .= '</div>';
        $html .= "</div>";
        $html .= '</div>';

        return $html;

    }

    public static function getAssociationName($associationType=null,$associationId=null)
    {
        $app = \Cobalt\Container::fetch('app');
        $associationType = $associationType ? $associationType : $app->input->get('association_type');
        $associationId = $associationId ? $associationId : $app->input->get('association_id');

        $db = \Cobalt\Container::fetch('db');
        $query = $db->getQuery(true);

        switch ($associationType) {
            case "company":
                $select = "name";
                $table = "companies";
            break;
            case "person":
                $select = "CONCAT(first_name,' ',last_name)";
                $table = "people";
            break;
            case "deal":
                $select = "name";
                $table = "deals";
            break;
        }

        $query->select($select)->from("#__".$table)->where("id=".$associationId);
        $db->setQuery($query);

        $name = $db->loadResult();

        return $name;
    }

}
