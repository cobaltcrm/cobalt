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

use JText;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

 class TextHelper extends JText
 {

     public static function _($string, $jsSafe = false, $interpretBackSlashes = true, $script = false)
    {
        $string = JText::_($string, $jsSafe, $interpretBackSlashes, $script);

        $configValues = ConfigHelper::getNamingConventions();

        foreach ($configValues as $key => $value) {
            $key = str_replace("lang_","",$key);
            $string = self::ext_str_ireplace($key,$value,$string);
               if ( stripos($string,"people") !== FALSE ) {
                    $string = self::ext_str_ireplace("people",$configValues["lang_persons"],$string);
               }
        }

        return $string;
    }

  public static function script($string = NULL, $jsSafe = false, $interpretBackSlashes = true)
     {
          $newString = JText::_($string, $jsSafe, $interpretBackSlashes);

          $configValues = ConfigHelper::getNamingConventions();

          foreach ($configValues as $key => $value) {
               $key = str_replace("lang_","",$key);
               $newString = self::ext_str_ireplace($key,$value,$newString);
          }

          // Normalize the key and translate the string.
          parent::$strings[strtoupper($string)] = $newString;

          return parent::$strings;
     }

    public static function ext_str_ireplace($findme, $replacewith, $subject)
     {
          // Replaces $findme in $subject with $replacewith
          // Ignores the case and do keep the original capitalization by using $1 in $replacewith
          // Required: PHP 5

          $rest = $subject;
          $result = '';

          while (stripos($rest, $findme) !== false) {
               $pos = stripos($rest, $findme);

               // Remove the wanted string from $rest and append it to $result
               $result .= substr($rest, 0, $pos);
               $rest = substr($rest, $pos, strlen($rest)-$pos);

               // Remove the wanted string from $rest and place it correctly into $result
               $result .= str_replace('$1', substr($rest, 0, strlen($findme)), $replacewith);
               $rest = substr($rest, strlen($findme), strlen($rest)-strlen($findme));
          }

          // After the last match, append the rest
          $result .= $rest;

          return $result;
     }

 }
