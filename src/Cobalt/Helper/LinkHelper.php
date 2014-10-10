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

use Cobalt\Helper\RouteHelper;

/**
 * Class to build link to specific items
 *
 * Class LinkHelper
 * @package Cobalt\Helper
 */
class LinkHelper
{
    /**
     * Link to view deal
     *
     * @param $deal_id
     * @return string
     */
    public static function viewDeal($deal_id, array $params = array())
    {
        $link = array(
            'view' => 'deal',
            'layout' => 'deal',
            'id' => intval($deal_id)
        );

        $query = array_merge($link, $params);

        return self::create($query);
    }

    /**
     * Link to view person
     *
     * @param $person_id
     * @return string
     */
    public static function viewPerson($person_id, array $params = array())
    {
        $link = array(
            'view' => 'people',
            'layout' => 'person',
            'id' => intval($person_id)
        );

        $query = array_merge($link, $params);

        return self::create($query);
    }

    /**
     * Link to view Company
     *
     * @param $copmany_id
     * @return string
     */
    public static function viewCompany($copmany_id, array $params = array())
    {
        $link = array(
            'view' => 'companies',
            'layout' => 'company',
            'id' => intval($copmany_id)
        );

        $query = array_merge($link, $params);

        return self::create($query);
    }

    /**
     * Build Custom URL by array of keys and values
     *
     * @param array $query
     * @return string
     */
    public static function create(array $query)
    {
        return RouteHelper::_('index.php?'.http_build_query($query));
    }
}