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

class RouteHelper
{
    public static function _($route)
    {
        $route = parse_url($route);
        $sef = self::baseUrl();

        if (!isset($route['query']))
        {
            return $sef;
        }

        parse_str($route['query'], $attrs);

        // generate SEF URI from view, layout and id
        if (isset($attrs['view']) && $attrs['view'])
        {
            $sef .= $attrs['view'];
            unset($attrs['view']);

            if (isset($attrs['layout']) && $attrs['layout'])
            {
                $sef .= '/' . $attrs['layout'];
                unset($attrs['layout']);

                if (isset($attrs['id']) && $attrs['id'])
                {
                    $sef .= '/' . $attrs['id'];
                    unset($attrs['id']);
                }
            }
        }

        // add unknown URI params back
        if ($attrs)
        {
            $sef .= '?' . http_build_query($attrs);
        }

        return  $sef;
    }

    public static function baseUrl()
    {
        $currPath = $_SERVER['PHP_SELF'];
        $pathInfo = pathinfo($currPath);
        $hostName = $_SERVER['HTTP_HOST'];
        $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https://'?'https://':'http://';

        return $protocol.$hostName.$pathInfo['dirname']."/";
    }

}
