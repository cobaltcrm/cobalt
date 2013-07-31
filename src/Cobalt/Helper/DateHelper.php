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

 class DateHelper
 {
     /**
      * Get timezone dropdown info for profile
      */
     public static function getTimezones()
     {
             $list = timezone_identifiers_list();
             $zones =  array();

             foreach ($list as $zone) {
                $zones[$zone] = $zone;
             }

            return $zones;
     }

     /*
      * Get date formates for profile
      */
     public static function getDateFormats()
     {
         return array( 'm/d/y' => 'mm/dd/yy', 'd/m/y' => 'dd/mm/yy' );
     }

     /*
      * Get time formates for profile
      */
     public static function getTimeFormats()
     {
         return array( 'g:i A' => '7:30 PM', 'H:i' => '19:30' );
     }

     /*
      * Get goal dates for filtering
      */
     public static function getGoalDates()
     {
         return array(     'this_week'     => TextHelper::_("COBALT_THIS_WEEK"),
                            'next_week'     => TextHelper::_("COBALT_NEXT_WEEK"),
                            'this_month'    => TextHelper::_("COBALT_THIS_MONTH"),
                            'next_month'    => TextHelper::_("COBALT_NEXT_MONTH"),
                            'this_quarter'  => TextHelper::_("COBALT_THIS_QUARTER"),
                            'next_quarter'  => TextHelper::_("COBALT_NEXT_QUARTER"),
                            'this_year'     => TextHelper::_("COBALT_THIS_YEAR"),
                            'custom'        => TextHelper::_("COBALT_CUSTOM") );
     }

     /*
      * Get Created dates for filtering
      */
     public static function getCreatedDates()
     {
         return array(     'this_week'     => TextHelper::_("COBALT_THIS_WEEK"),
                            'last_week'     => TextHelper::_("COBALT_LAST_WEEK"),
                            'this_month'    => TextHelper::_("COBALT_THIS_MONTH"),
                            'last_month'    => TextHelper::_("COBALT_LAST_MONTH"),
                            'today'         => TextHelper::_("COBALT_TODAY"),
                            'yesterday'     => TextHelper::_("COBALT_YESTERDAY"), );
     }

     /*
      * Get the weeks in a month for report page data creation
      */
     public static function getWeeksInMonth($current_month)
     {
            $month = intval(date('m',strtotime($current_month))); //force month to single integer if '0x'
            $year = intval(date('Y',strtotime($current_month)));
            $suff = array('st','nd','rd','th','th','th'); //week suffixes
            $end = date('t',mktime(0,0,0,$month,1,$year)); //last date day of month: 28 - 31
            $start = date('w',mktime(0,0,0,$month,1,$year)); //1st day of month: 0 - 6 (Sun - Sat)
            $last = 7 - $start; //get last day date (Sat) of first week
            $noweeks = ceil((($end - ($last + 1))/7) + 1); //total no. weeks in month
            $output = ""; //initialize string
            $weeks = array();
            $monthlabel = str_pad($month, 2, '0', STR_PAD_LEFT);
            for ($x=1;$x<$noweeks+1;$x++) {
                if ($x == 1) {
                    $startdate = "$year-$monthlabel-01";
                    $day = $last - 6;
                } else {
                    $day = $last + 1 + (($x-2)*7);
                    $day = str_pad($day, 2, '0', STR_PAD_LEFT);
                    $startdate = "$year-$monthlabel-$day";
                }
                if ($x == $noweeks) {
                    $enddate = "$year-$monthlabel-$end";
                } else {
                    $dayend = $day + 6;
                    $dayend = str_pad($dayend, 2, '0', STR_PAD_LEFT);
                    $enddate = "$year-$monthlabel-$dayend";
                }
                    $output .= "{$x}{$suff[$x-1]} week -> Start date=$startdate End date=$enddate <br />";
                    $weeks[] = array ( 'start_date'=>$startdate,'end_date'=>$enddate );
            }

            return $weeks;
     }

    /*
     * Get month names for report page charts and data creation
     */
    public static function getMonthNames()
    {
        return array(      TextHelper::_('COBALT_JANUARY'),
                           TextHelper::_('COBALT_FEBRUARY'),
                           TextHelper::_('COBALT_MARCH'),
                           TextHelper::_('COBALT_APRIL'),
                           TextHelper::_('COBALT_MAY'),
                           TextHelper::_('COBALT_JUNE'),
                           TextHelper::_('COBALT_JULY'),
                           TextHelper::_('COBALT_AUGUST'),
                           TextHelper::_('COBALT_SEPTEMBER'),
                           TextHelper::_('COBALT_OCTOBER'),
                           TextHelper::_('COBALT_NOVEMBER'),
                           TextHelper::_('COBALT_DECEMBER')  );
    }

    /*
     * Get abbreviated month names for report page charts and data creation
     */
    public static function getMonthNamesShort()
    {
        return array(   TextHelper::_('COBALT_JAN'),
                        TextHelper::_('COBALT_FEB'),
                        TextHelper::_('COBALT_MAR'),
                        TextHelper::_('COBALT_APR'),
                        TextHelper::_('COBALT_MAY'),
                        TextHelper::_('COBALT_JUN'),
                        TextHelper::_('COBALT_JUL'),
                        TextHelper::_('COBALT_AUG'),
                        TextHelper::_('COBALT_SEP'),
                        TextHelper::_('COBALT_OCT'),
                        TextHelper::_('COBALT_NOV'),
                        TextHelper::_('COBALT_DEC')  );
    }

    /*
     * Get month start and end dates and names for report page charts and data creation
     */
    public static function getMonthDates()
    {
        $current_year = date('Y-01-01 00:00:00');
        $month_names = self::getMonthNames();
        $months = array();
        for ($i=0; $i<12; $i++) {
                $months[$i] = array( 'name'=>$month_names[$i],'date'=>date('Y-m-d 00:00:00',strtotime("$current_year + $i months")));
            }

        return $months;
    }

    public static function getTimeIntervals()
    {
        $starttime = "7:00:00";
        $temptime = strtotime($starttime);
        $nextday = strtotime($starttime." + 1 day");

        $times = array();
        do {
              $times[date("H:i:s",$temptime)] = date(UsersHelper::getTimeFormat(),$temptime);
              $temptime = date("Y-m-d H:i:s",$temptime+(15*60));
              $temptime = strtotime($temptime);
        } while ($temptime<$nextday);

        return $times;

    }

    public static function getSiteTimezone()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select("timezone")->from("#__config")->where("id=1");
        $db->setQuery($query);

        return $db->loadResult();

    }

    public static function formatDBDate($date, $time=true)
    {
        $userTz = UsersHelper::getTimezone();
        $dateTime = new \DateTime($date, new \DateTimeZone($userTz));

        $utc = "UTC";
        $dateTime->setTimeZone(new \DateTimeZone($utc));

        if (!$time) {
            $mysql = "Y-m-d";
        } else {
            $mysql = "Y-m-d H:i:s";
        }

        $time = $dateTime->format($mysql);

        return $time;
    }

    public static function formatDate($date,$time=false,$useUserDateFormat=TRUE)
    {
      if ( strtotime($date) <= 0 ) {
        return "";
      }

      try {

        $dateTime = DateHelper::convertDateToUserTimezone($date);
        $date_format = UsersHelper::getDateFormat();

        if ($useUserDateFormat) {
          $date = $dateTime->format($date_format);
        } else {
          $date =  $dateTime->format("Y-m-d H:i:s");
        }

        if ($time) {
            if ( !(date("H:i:s",strtotime($date))=="00:00:00")) {
              if ( is_string($time) ) {
                $time_format = date($time,strtotime($date));
              } else {
                $time_format = self::formatTime($date);
              }
              $date .=' '.$time_format;
            }
        }

        return $date;
      } catch (\Exception $e) {
        return "";
      }

    }

    public static function formatDateString($date)
    {
      $dateFormat = UsersHelper::getDateFormat();

      return date($dateFormat,strtotime($date));
    }

    public static function formatTimeString($time,$timeFormatOverride=null)
    {
      $timeFormat = $timeFormatOverride ? $timeFormatOverride : UsersHelper::getTimeFormat();

      return date($timeFormat,strtotime($time));
    }

    public static function formatTime($time,$timeFormatOverride=null)
    {
          $exp = explode(" ",$time);
          if ( count($exp) < 1 ) {
            $time = "0000-00-00 ".$time;
          }

          $time_format = $timeFormatOverride ? $timeFormatOverride : UsersHelper::getTimeFormat();
          $dateTime = self::convertDateToUserTimezone($time);

          return $dateTime->format($time_format);

    }

    public static function convertDateToUserTimezone($date,$returnString=false)
    {
        $userTz = UsersHelper::getTimezone();
        $utc = "UTC";

        $dateTime = new \DateTime($date, new \DateTimeZone($utc));
        $dateTime->setTimezone(new \DateTimeZone($userTz));

        return $returnString ? $dateTime->format("Y-m-d H:i:s") : $dateTime;

    }

    public static function getCurrentTime($string=FALSE,$showTime=FALSE)
    {
        $timezone = UsersHelper::getTimezone();
        $current    = new \DateTime();
        $current->setTimezone(new \DateTimeZone($timezone));
        if ($string) {
          $format = $showTime ? "Y-m-d H:i:s" : "Y-m-d";

          return $current->format($format);
        }

        return $current;
    }

    public static function getElapsedTime($date,$showDays=TRUE,$showMonths=TRUE,$showYears=TRUE, $showHours=TRUE, $showMinutes=TRUE,$showSeconds=FALSE)
    {
        $time = time() - strtotime($date); // to get the time since that moment

        $tokens = array (
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );

        foreach ($tokens as $unit => $text) {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);

            return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
        }

        return DateHelper::formatDate($date);

    }

    public static function pluralize( $count, $text )
    {
        return $count . ( ( $count == 1 ) ? ( " $text " ) : ( " ${text}s " ) );
    }

    public static function x_week_range(&$start_date, &$end_date, $date)
    {
      $ts = strtotime($date);
      $start = (date('w', $ts) == 0) ? $ts : strtotime('last sunday', $ts);
      $start_date = date('Y-m-d H:i:s', $start);
      $end_date = date('Y-m-d H:i:s', strtotime('next saturday', $start));
    }

    public static function getRelativeDate($display_date)
    {

            $now                  = self::getCurrentTime(TRUE,FALSE);
            $tomorrow             = self::getCurrentTime(TRUE,FALSE)." + 1 days";
            $day_after_tomorrow   = self::getCurrentTime(TRUE,FALSE)." + 2 days";

            $this_week            = null;
            $next_week            = null;
            self::x_week_range($this_week,$next_week,self::getCurrentTime(true,FALSE));

            $week_after_next = null;
            $week_after_next_week = null;
            self::x_week_range($week_after_next,$week_after_next_week,self::getCurrentTime(true,FALSE)." + 1 week");

            $next_month           = date("Y-m-1",strtotime(self::getCurrentTime(TRUE,FALSE)." +1 month"));
            $next_next_month      = date("Y-m-1",strtotime(self::getCurrentTime(TRUE,FALSE)." +2 months"));

            if (strtotime($display_date) < strtotime($now)) {
                $current_heading = TextHelper::_('COBALT_LATE_ITEMS');
            } elseif (strtotime($display_date) >= strtotime($now) && strtotime($display_date) < strtotime($tomorrow) ) {
                $current_heading = TextHelper::_('COBALT_TODAY');
            } elseif (strtotime($display_date) >= strtotime($tomorrow) && strtotime($display_date) < strtotime($day_after_tomorrow) ) {
                $current_heading = TextHelper::_('COBALT_TOMORROW');
            } elseif (strtotime($display_date) >= strtotime($day_after_tomorrow) && strtotime($display_date) < strtotime($week_after_next)) {
                $current_heading = TextHelper::_('COBALT_THIS_WEEK');
            } elseif (strtotime($display_date) >= strtotime($week_after_next) && strtotime($display_date) < strtotime($week_after_next_week)) {
                $current_heading = TextHelper::_('COBALT_NEXT_WEEK');
            } elseif (strtotime($display_date) >= strtotime($week_after_next_week) && strtotime($display_date) < strtotime($next_month)) {
                $current_heading = TextHelper::_('COBALT_THIS_MONTH');
            } elseif (strtotime($display_date) >= strtotime($next_month) && strtotime($display_date) < strtotime($next_next_month)) {
                $current_heading = TextHelper::_('COBALT_NEXT_MONTH');
            } else {
                $current_heading = TextHelper::_('COBALT_IN_THE_FUTURE');
            }

        return $current_heading;
    }

}
