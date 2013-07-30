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

 class CobaltHelperDeal
 {

    public static function getDeal($id)
    {
        //get db object
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //generate query
        $query->select("name,id FROM #__deals");
        $query->where('id='.$id);
        $db->setQuery($query);

        //return results
        $row = $db->loadAssocList();

        return $row;

    }

    //function to return filter types for deals
    public static function getDealTypes()
    {
        return array(   'all'=>CRMText::_('COBALT_ALL_DEALS'),
                        'today'=>CRMText::_('COBALT_DEALS_TASKS_TODAY'),
                        'tomorrow'=>CRMText::_('COBALT_DEALS_TASKS_TOMORROW'),
                        'updated_thirty'=>CRMText::_('COBALT_DEALS_UPDATED_LAST_MONTH'),
                        'valuable'=>CRMText::_('COBALT_DEALS_MOST_VALUABLE'),
                        'past'=>CRMText::_('COBALT_PAST_DUE_DEALS'),
                        'not_updated_thirty'=>CRMText::_('COBALT_DEALS_NOT_UPDATED'),
                        'shared'=>CRMText::_('COBALT_SHARED_DEALS'),
                        'archived'=>CRMText::_('COBALT_ARCHIVED_DEALS'));
    }

    //function to return deal stages
    public static function getStages($stage_name=null,$stagesOnly=FALSE,$idsOnly=TRUE)
    {
        //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //query
        $query->select("*");
        $query->from("#__stages");

        if ($stage_name) {
            $query->where("LOWER(name) LIKE '%".ucwords($stage_name)."%'");
        }

        if (!$stagesOnly) {
            $base = array( 'all'=>CRMText::_('COBALT_ALL_STAGES'),'active'=>CRMText::_('COBALT_ACTIVE_STAGES'));
        } else {
            $base = array();
        }

        $query->order('ordering');

        $db->setQuery($query);
        $results = $db->loadAssocList();

        if ($idsOnly) {
            $stages = array();
            foreach ($results as $key => $stage) {
                $stages[$stage['id']] = $stage['name'];
            }
        } else {
            $stages = $results;
        }

        return $base + $stages;
    }

    //function to return deal stages
    public static function getNonInactiveStages()
    {
        //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //query
        $query->select("id");
        $query->from("#__stages");
        $query->order('ordering');
        $query->where('percent>0');

        $db->setQuery($query);
        $results = $db->loadColumn();

        return $results;
    }

    public static function getPrimaryContact($deal_id)
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select("primary_contact_id")->from("#__deals")->where("id=".$deal_id);
        $db->setQuery($query);

        return $db->loadResult();
    }

    //get stages for sorting sources
    public static function getSourceStages()
    {
        //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //query
        $query->select("*");
        $query->from("#__stages");

        //filter by active and closed stages
        $inactive_stage_ids = CobaltHelperDeal::getInactiveStages();
        $query->where("id NOT IN(".implode(',',$inactive_stage_ids).")");

        $query->order('ordering');

        //merge arrays
        $base = array ( 'all'=>'all stages','active'=>'active stages');
        $db->setQuery($query);
        $results = $db->loadAssocList();
        $stages = array();
        if ( count($results) > 0 ) {
            foreach ($results as $key => $stage) {
                $stages[$stage['id']] = $stage['name'];
            }
        }

        return $base + $stages;
    }

    //function to return active stages
    public static function getActiveStages($idsOnly=FALSE)
    {
        //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //query
        $query->select("*");
        $query->from("#__stages");
        $query->where("percent > 0");
        $query->where("percent < 100");
        $query->where("won=0");

        $query->order('ordering');
        //return results
        $db->setQuery($query);
        $results = $db->loadAssocList();

        $stages = array();
        if ($idsOnly) {
            if ( count($results) > 0 ) {
                foreach ($results as $key => $stage) {
                    $stages[$stage['id']] = $stage['name'];
                }
            }
        } else {
            return $results;
        }

        return $stages;
    }

    //function to return deal stages
    public static function getGoalStages()
    {
        //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //query
        $query->select("*");
        $query->from("#__stages");

        $query->order('ordering');

        //merge arrays
        $db->setQuery($query);
        $results = $db->loadAssocList();
        $stages = array();
        foreach ($results as $key => $stage) {
            $stages[$stage['id']] = $stage['name'];
        }

        return $stages;
    }

    public static function getDealFilters()
    {
        return array(  'all'=>strtolower(CRMText::_('COBALT_ALL')),
                        'this_week'=>strtolower(CRMText::_('COBALT_THIS_WEEK')),
                        'next_week'=>strtolower(CRMText::_('COBALT_NEXT_WEEK')),
                        'this_month'=>strtolower(CRMText::_('COBALT_THIS_MONTH')),
                        'next_month'=>strtolower(CRMText::_('COBALT_NEXT_MONTH')));
    }

    //get closing filters for deals
    public static function getClosing()
    {
        return CobaltHelperDeal::getDealFilters();
    }

    //get closing filters for deals
    public static function getModified()
    {
         return CobaltHelperDeal::getDealFilters();
    }

    //get closing filters for deals
    public static function getCreated()
    {
        return CobaltHelperDeal::getDealFilters();
    }

    /**
     * Get amounts for dropdowns
     */
    public static function getAmounts()
    {
        return array(  'small' => CRMText::_('COBALT_SMALL'), 'medium' => CRMText::_('COBALT_MEDIUM'), 'large' => CRMText::_('COBALT_LARGE') );
    }

    //get won stage
    public static function getWonStages()
    {
        //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //search for 100% stage id
        $query->select("s.id")->from("#__stages AS s")->where("s.won=1");

        //return id
        $db->setQuery($query);

        $results = $db->loadColumn();
        $results[] = 0;

        return $results;
    }

    //get lost stage
    public static function getInactiveStages()
    {
        //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //search for 0% stage id
        $query->select("s.id")->from("#__stages AS s")->where('s.percent=0');

        //return id
        $db->setQuery($query);
        $stages = $db->loadColumn();

        $base = array(0);

        return $stages + $base;
    }

    public static function getClosedStages()
    {
        //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //search for 100% stage id
        $query->select("s.id")->from("#__stages AS s")->where('s.percent=100');

        //return id
        $db->setQuery($query);
        $results = $db->loadColumn();

        if ( count($results) > 0 ) {
            return $results;
        } else {
            return array();
        }

    }

    //get deal statuses
    public static function getStatuses($status_name=null,$classOnly=FALSE)
    {
        //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //query
        $query->select("*");
        $query->from("#__deal_status");

        if ($status_name) {
            $query->where("name LIKE '%".$status_name."%'");
        }

        $query->order('ordering');

        //merge arrays
        $db->setQuery($query);
        $results = $db->loadAssocList();
        $statuses = array();

        if ($classOnly) {
            $statuses[0] = 'none';
        } else {
            $statuses[0] = CRMText::_('COBALT_NONE_STATUS');
        }

        if ( count($results) > 0 ) {
            foreach ($results as $key => $status) {
                if ($classOnly) {
                    $statuses[$status['id']] = $status['class'];
                } else {
                    $statuses[$status['id']] = CRMText::_('COBALT_'.strtoupper($status['name']).'_STATUS');
                }
            }
        }

        return $statuses;
    }

    //get deal sources
    public static function getSources($source_name=null)
    {
        //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //query
        $query->select("*");
        $query->from("#__sources");

        $query->order('ordering');

        if ($source_name) {
            $query->where("name LIKE '%".$source_name."%'");
        }

        //merge arrays
        $db->setQuery($query);
        $results = $db->loadAssocList();
        $sources = array();
        foreach ($results as $key => $source) {
            $sources[$source['id']] = $source['name'];
        }

        return $sources;
    }

    //get user created custom fields from database
    public static function getUserCustomFields($id=null)
    {
        //get dbo
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //gen query string
        $query->select("*");
        $query->from("#__deal_custom");

        //specific field
        if ($id) {
            $query->where("id=$id");
        }

        //run query and return results
        $db->setQuery($query);

        return $db->loadAssocList();

    }

    //get all custom fields for reports
    public static function getAllCustomFields()
    {
        $base = array (
            "summary"                       => ucwords(CRMText::_("COBALT_SUMMARY")),
            "amount"                        => ucwords(CRMText::_("COBALT_AMOUNT")),
            "name"                          => ucwords(CRMText::_("COBALT_DEALS_NAME")),
            "owner_id"                      => ucwords(CRMText::_("COBALT_DEAL_OWNER")),
            "stage_id"                      => ucwords(CRMText::_("COBALT_DEAL_STAGE")),
            "probability"                   => ucwords(CRMText::_("COBALT_DEAL_PROBABILITY")),
            "status_id"                     => ucwords(CRMText::_("COBALT_DEAL_STATUS")),
            "expected_close"                => ucwords(CRMText::_("COBALT_DEAL_CLOSE")),
            "modified"                      => ucwords(CRMText::_("COBALT_MODIFIED")),
            "created"                       => ucwords(CRMText::_("COBALT_CREATED")),
            "source_id"                     => ucwords(CRMText::_("COBALT_REPORTS_SOURCE")),
            "actual_close"                  => ucwords(CRMText::_("COBALT_DEALS_ACTUAL_CLOSE")),
            "primary_contact_name"          => ucwords(CRMText::_("COBALT_PRIMARY_CONTACT_NAME")),
            "primary_contact_email"         => ucwords(CRMText::_("COBALT_PRIMARY_CONTACT_EMAIL")),
            "primary_contact_phone"         => ucwords(CRMText::_("COBALT_PRIMARY_CONTACT_PHONE")),
            "primary_contact_city"          => ucwords(CRMText::_("COBALT_PRIMARY_CONTACT_CITY")),
            "primary_contact_state"         => ucwords(CRMText::_("COBALT_PRIMARY_CONTACT_STATE")),
            "primary_contact_company_name"  => ucwords(CRMText::_("COBALT_PRIMARY_CONTACT_COMPANY_NAME")),
            "company_name"                  => ucwords(CRMText::_("COBALT_DEAL_COMPANY"))
        );
        $custom = CobaltHelperDeal::getUserCustomFields();
        for ( $i=0; $i<count($custom); $i++ ) {
            $field = $custom[$i];
            $base["custom_".$field['id']] = $field['name'];
        }

        return $base;
    }

    //get column filters
    public static function getColumnFilters()
    {
        return array(   'company'           => ucwords(CRMText::_('COBALT_DEALS_COMPANY')),
                        'primary_contact'   => ucwords(CRMText::_('COBALT_PRIMARY_CONTACT')),
                        'contacts'          => ucwords(CRMText::_('COBALT_DEALS_CONTACTS')),
                        'summary'           => ucwords(CRMText::_('COBALT_DEALS_SUMMARY')),
                        'amount'            => ucwords(CRMText::_('COBALT_DEALS_AMOUNT')),
                        'status'            => ucwords(CRMText::_('COBALT_DEALS_STATUS')),
                        'stage'             => ucwords(CRMText::_('COBALT_DEALS_STAGE')),
                        'source'            => ucwords(CRMText::_('COBALT_DEAL_SOURCE')),
                        'expected_close'    => ucwords(CRMText::_('COBALT_DEALS_EXPECTED_CLOSE')),
                        'actual_close'      => ucwords(CRMText::_('COBALT_DEALS_ACTUAL_CLOSE')),
                        'owner'             => ucwords(CRMText::_('COBALT_DEALS_OWNER')),
                        'next_action'       => ucwords(CRMText::_('COBALT_DEALS_NEXT')),
                        'deals_due'         => ucwords(CRMText::_('COBALT_DEALS_DUE')),
                        'notes'             => ucwords(CRMText::_('COBALT_DEALS_NOTES')),
                        'created'           => ucwords(CRMText::_('COBALT_PEOPLE_ADDED')),
                        'modified'          => ucwords(CRMText::_('COBALT_PEOPLE_UPDATED'))
                    );
    }

    //get selected column filters
    public static function getSelectedColumnFilters()
    {
        //get the user session data
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select("deals_columns");
        $query->from("#__users");
        $query->where("id=".CobaltHelperUsers::getUserId());
        $db->setQuery($query);
        $results = $db->loadResult();

        //unserialize columns
        $columns = unserialize($results);
        if ( is_array($columns) ) {
            return $columns;
        } else {
            //if it is empty then load a default set
            return CobaltHelperDeal::getDefaultColumnFilters();
        }
    }

    //get default column filters
    public static function getDefaultColumnFilters()
    {
        return array( 'company','primary_contact','amount','stage','expected_close','next_action','deals_due','notes','created','modified' );
    }

    public static function downloadDocument()
    {
        $model = new CobaltModelDocument();
        $document = $model->getDocument();

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($document->name));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($document->path));
        ob_clean();
        flush();
        readfile($document->path);
        exit;
    }

 }
