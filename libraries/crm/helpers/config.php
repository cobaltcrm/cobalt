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
defined( '_CEXEC' ) or die( 'Restricted access' );

 class CobaltHelperConfig extends JObject
 {

     public static function getImapConfig()
     {
         $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select("imap_host,imap_pass,imap_user")->from("#__config")->where("id=1");
        $db->setQuery($query);
        $result =  $db->loadObject();
        $result->imap_pass = base64_decode($result->imap_pass);

        return $result;
     }

     /**
     * Get the configuration value for the specified field. If the field was not found in the config, return null.
     *
     * @param  string  $field           The name of the field
     * @param  boolean $serializedArray True if the field is stored as an array
     * @return mixed   The value of the field in the #__jf_configuration table
     */
    public static function getConfigValue($field,$serializedArray=FALSE)
    {

        $configModel = new CobaltModelConfig();
        $config = $configModel->getConfig(TRUE);

        if (is_array($config) && array_key_exists($field, $config)) {
            $value = $serializedArray == TRUE && $config[$field] != 0 && $config[$field] != "0" ? unserialize($config[$field]) : $config[$field];
        } else {
            $value = null;
        }

        return $value;
    }

    public static function getVersion()
    {
        $xml = JFactory::getXML( 'simple' );

         if ( file_exists(JPATH_SITE.'/administrator/cobalt.xml')) {
             $xml->loadFile( JPATH_SITE.'/administrator/cobalt.xml' );
            $position = $xml->document->getElementByPath(  'version' );

            return $position->data();
         } else {
             return 0;
         }

    }

    public static function getNamingConventions()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select("lang_deal,lang_person,lang_company,lang_contact,lang_lead,lang_task,lang_event,lang_goal")
                ->from("#__config")
                ->where("id=1");
        $db->setQuery($query);
        $result =  $db->loadObject();

        $names = array();
        if ( count($result) > 0 ) {
            foreach ($result as $key => $value) {
                $names[$key] = $value;
                $names[self::pluralize($key)] = self::pluralize($value);
            }
        }

        return $names;
    }

      public static function pluralize( $string )
    {
         $plural = array(
            array( '/(quiz)$/i',               "$1zes"   ),
            array( '/^(ox)$/i',                "$1en"    ),
            array( '/([m|l])ouse$/i',          "$1ice"   ),
            array( '/(matr|vert|ind)ix|ex$/i', "$1ices"  ),
            array( '/(x|ch|ss|sh)$/i',         "$1es"    ),
            array( '/([^aeiouy]|qu)y$/i',      "$1ies"   ),
            array( '/([^aeiouy]|qu)ies$/i',    "$1y"     ),
            array( '/(hive)$/i',               "$1s"     ),
            array( '/(?:([^f])fe|([lr])f)$/i', "$1$2ves" ),
            array( '/sis$/i',                  "ses"     ),
            array( '/([ti])um$/i',             "$1a"     ),
            array( '/(buffal|tomat)o$/i',      "$1oes"   ),
            array( '/(bu)s$/i',                "$1ses"   ),
            array( '/(alias|status)$/i',       "$1es"    ),
            array( '/(octop|vir)us$/i',        "$1i"     ),
            array( '/(ax|test)is$/i',          "$1es"    ),
            array( '/s$/i',                    "s"       ),
            array( '/$/',                      "s"       )
            );

            $irregular = array(
            array( 'move',   'moves'    ),
            array( 'sex',    'sexes'    ),
            array( 'child',  'children' ),
            array( 'man',    'men'      ),
            array( 'person', 'people'   )
            );

            $uncountable = array(
            'sheep',
            'fish',
            'series',
            'species',
            'money',
            'rice',
            'information',
            'equipment'
        );

        // save some time in the case that singular and plural are the same
        if ( in_array( strtolower( $string ), $uncountable ) )
        return $string;

        // check for irregular singular forms
        foreach ($irregular as $noun) {
        if ( strtolower( $string ) == $noun[0] )
            return $noun[1];
        }

        // check for matches using regular expressions
        foreach ($plural as $pattern) {
        if ( preg_match( $pattern[0], $string ) )
            return preg_replace( $pattern[0], $pattern[1], $string );
        }

        return $string;

    }

    public static function getCurrency()
    {
        return self::getConfigValue('currency');
    }

    public static function checkAcymailing()
    {
        jimport('joomla.filesystem.folder');
        if ( JFolder::exists(JPATH_ROOT.'/administrator/components/com_acymailing') ) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function getLanguages()
    {
        jimport('joomla.filesystem.folder');
        $dirs = JFolder::folders(JPATH_SITE."/language");

        $ret = array();
        if ( count($dirs) > 0 ) {
            foreach ( $dirs as $lang )
                $ret[$lang] = $lang;
        }

        return $ret;

    }

    public static function getLanguage()
    {
        $config = JFactory::getConfig();

        return $config->get('language');

    }

    public static function saveLanguage($lang)
    {
        $config = JFactory::getConfig();
        $config->set("language",$lang);

        $file = JPATH_BASE."/configuration.php";
        file_put_contents($file, $config->toString('PHP', array('class' => 'JConfig', 'closingtag' => false)));

    }

}
