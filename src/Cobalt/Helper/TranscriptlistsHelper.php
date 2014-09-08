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

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class TranscriptlistsHelper
{
    public static function getRooms($associationId=null,$associationType=null)
    {
        $app = \Cobalt\Container::fetch('app');

        $autoId = $app->input->get('id') ? $app->input->get('id') : $app->input->get('association_id');
        $autoType = $app->input->get('layout') ? $app->input->get('layout') : $app->input->get('association_type');

        $associationId = $associationId ? $associationId : $autoId;
        $associationType = $associationType ? $associationType : $autoType;

        $db = \Cobalt\Container::fetch('db');
        $query = $db->getQuery(true);

        $query->select("id,name")
            ->from("#__banter_rooms")
            ->where("association_id=".$associationId)
            ->where("association_type='".$associationType."'");

        $db->setQuery($query);

        $rooms =  $db->loadObjectList();

        return $rooms;

    }

    public static function getTranscripts($roomId=null)
    {
        $app = \Cobalt\Container::fetch('app');

        $roomId = $roomId ? $roomId : $app->input->get('room_id');

        $db = \Cobalt\Container::fetch('db');
        $query = $db->getQuery(true);

        $query->select("t.*,r.name AS room_name")
            ->from("#__banter_transcripts AS t")
            ->leftJoin("#__banter_rooms AS r ON r.id = t.room_id")
            ->where("t.room_id=".$roomId);

        $db->setQuery($query);
        $transcripts = $db->loadObjectList();

        return $transcripts;

    }

}
