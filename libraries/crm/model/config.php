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

class CobaltModelConfig extends CobaltModelDefault
{

    function store($data=null)
    {

        $app = JFactory::getApplication();

        //Load Tables
        $row = JTable::getInstance('config','Table');
        $data = isset($data) && is_array($data) && count($data) > 0 ? $data : $app->input->getRequest( 'post' );

        //date generation
        $date = date('Y-m-d H:i:s');

        $data['id'] = 1;

        if ( !array_key_exists('id',$data) ){
            $data['created'] = $date;
        }

        $data['modified'] = $date;

        if ( array_key_exists('imap_pass',$data) ){
            $data['imap_pass'] = base64_encode($data['imap_pass']);
        }

        $data['show_help'] = array_key_exists('show_help',$data) ? $data['show_help'] : 0;

        if ( array_key_exists("site_language",$data) ){
            CobaltHelperConfig::saveLanguage($data['site_language']);
            unset($data['site_language']);
        }

        // Bind the form fields to the table
        if (!$row->bind($data)) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        // Make sure the record is valid
        if (!$row->check()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        // Store the web link table to the database
        if (!$row->store()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        return true;
    }

    function _buildQuery(){

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select("*")->from("#__config")->where("id=1");
        $db->setQuery($query);

        return $query;

    }

    function getConfig($array=FALSE){

        $db = JFactory::getDBO();
        $query = $this->_buildQuery();

        if ( $array ){
            $config = $db->loadAssoc();
            $config['imap_pass'] = base64_decode($config['imap_pass']);
        }else{
            $config = $db->loadObject();
            $config->imap_pass = base64_decode($config->imap_pass);
        }

        return $config;

    }

    /**
     * Get an RSS feed for the Changelog on Cobalt.com
     * @return array    The RSS feed and display parameters
     */
    function getUpdatesRSS()
    {
        $feed = array();

        // Parameters
        $feed['numItems']   = 2;
        $feed['Desc']       = 0;
        $feed['image']      = 0;
        $feed['itemDesc']   = 1;
        $feed['words']      = 60;
        $feed['title']      = 0;
        $feed['suffix']     = '';

        //  get RSS parsed object
        /*
        $options = array();
        $options['rssUrl']      = 'http://www.cobaltcrm.org/changelog.html?format=feed';
        $options['cache_time']  = 15 * 60;

        $rssDoc = JFactory::getXMLparser('RSS', $options);
        $feed['doc'] = $rssDoc;
        */
        $feed['doc'] = "";
        return $feed;
    }

}