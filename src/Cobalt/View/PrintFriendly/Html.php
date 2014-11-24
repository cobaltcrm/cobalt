<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\PrintFriendly;

use Cobalt\Factory;
use Joomla\View\AbstractHtmlView;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Html extends AbstractHtmlView
{
    public function render($tpl = null)
    {
        //app
        $app = Factory::getApplication();

        /* Model // function info */
        $model_name = $app->input->get('model');

        //layout
        $layout = $this->getLayout();

        //document
        $document = $app->getDocument();

        if (!$model_name) {
            switch ($layout) {
                case "person":
                    $model_name = "people";
                break;
                case "company":
                    $model_name = "companies";
                break;
                case "deal":
                    $model_name = "deal";
                break;
            }
        }

        if ($layout != "report") {
            $className = "\\Cobalt\\Model\\".ucwords($model_name);
            $model = new $className();
            $params = NULL;
            switch ($model_name) {
                case "company":
                    $func = "companies";
                break;
                case "deal":
                    $func = "deals";
                break;
                case "people":
                    $func = "people";
                break;
                case "event":
                case "events":
                    $func = "events";
                    if ($layout == "calendar") {
                        $params = "calendar";

                        //load js libs
                        $document->addScript( $app->get('uri.media.full').'js/fullcalendar.js' );
                        $document->addScript( $app->get('uri.media.full').'js/calendar_manager.js' );

                        //load required css for calendar
                        $document->addStyleSheet( $app->get('uri.media.full').'css/fullcalendar.css' );
                    }
                break;
            }
        }

        switch ($layout) {
            case "report":
                $report = $app->input->get('report');
                $item_id = ( $app->input->get('ids') ) ? $app->input->get('ids') : NULL;
                if ( is_array($item_id) ) {
                    $items = array();
                    foreach ($item_id as $key => $item) {
                        $items[] = $item;
                    }
                } else {
                    $items = $item_id;
                }
                switch ($report) {
                    case "sales_pipeline":
                        $model = Factory::getModel('Deal');
                        $model->set('_id',$items);
                        $state = $model->getState();
                        $reports = $model->getReportDeals();
                        $header = Factory::getView('reports','sales_pipeline_header',array('state'=>$state,'reports'=>$reports));
                        $table = Factory::getView('reports','sales_pipeline_filter',array('reports'=>$reports));
                        $footer = Factory::getView('reports','sales_pipeline_footer');
                    break;
                    case "source_report":
	                    $model = Factory::getModel('Deal');
                        $model->set('_id',$items);
                        $reports = $model->getDeals();
                        $state = $model->getState();
                        $header = Factory::getView('reports','sales_pipeline_header',array('state'=>$state,'reports'=>$reports));
                        $table = Factory::getView('reports','sales_pipeline_filter',array('reports'=>$reports));
                        $footer = Factory::getView('reports','source_report_footer');
                    break;
                    case "roi_report":
	                    $model = Factory::getModel('Source');
                        $model->set('_id',$items);
                        $sources = $model->getRoiSources();
                        $header = Factory::getView('reports','roi_report_header');
                        $table = Factory::getView('reports','roi_report_filter',array('sources'=>$sources));
                        $footer = Factory::getView('reports','roi_report_footer');
                    break;
                    case "notes":
	                    $model = Factory::getModel('Notes');
                        $model->set('_id',$items);
                        $note_entries = $model->getNotes(NULL,NULL,FALSE);
                        $state = $model->getState();
                        $header = Factory::getView('reports','notes_header',array('state'=>$state,'note_entries'=>$note_entries));
                        $table = Factory::getView('reports','notes_filter',array('note_entries'=>$note_entries));
                        $footer = Factory::getView('reports','notes_footer');
                    break;
                    case "custom_report":
	                    $model = Factory::getModel('Report');
                        $report = $model->getCustomReports($app->input->get('custom_report'));
                        $report_data = $model->getCustomReportData($app->input->get('custom_report'));
                        $state = $model->getState();
                        $data = array(
                                'report_data'=>$report_data,
                                'report'=>$report,
                               'state'=>$state
                            );
                        $header = Factory::getView('reports','custom_report_header',$data);
                        $table = Factory::getView('reports','custom_report_filter',$data);
                        $footer = Factory::getView('reports','custom_report_footer');
                    break;
                }
                $this->header = $header;
                $this->table = $table;
                $this->footer = $footer;
            break;
            default:
                /* Item info */
                $function = "get".ucwords($func);
                $item_id = ( $app->input->get('item_id') ) ? $app->input->get('item_id') : NULL;
                $ids = ( $app->input->get('ids') ) ? $app->input->get('ids') : NULL;

                $item_id = $item_id ? $item_id : $ids;

                if ( is_array($item_id) ) {
                    $items = array();
                    if ( $app->input->get('item_id') ) {
                        foreach ($item_id as $key => $item) {
                            $items[] = $key;
                        }
                    } else {
                        foreach ($item_id as $key => $item) {
                            $items[] = $item;
                        }
                    }
                } else {
                    $items = $item_id;
                }

                $model->set('_id',$items);
                $model->set("completed",null);
                $info = $model->$function($params);

                 /* Event list */
                $model = Factory::getModel('Event');
                $events = $model->getEvents("deal",NULL,$item_id);
                if (count($events)>0) {
                    $ref = array( 'events'=>$events,'print'=>TRUE );
                    $eventDock = Factory::getView('events','event_dock',$ref);
                    $this->event_dock = $eventDock;
                }

                /* Contact info */
                if (is_array($info) && array_key_exists(0,$info) && array_key_exists('people',$info[0]) && count($info[0]['people'])>0) {
	                $peopleModel = Factory::getModel('People');
                    $peopleModel->set('deal_id',$info[0]['id']);
                    $contacts = $peopleModel->getContacts();
                    $ref = array('contacts'=>$contacts,'print'=>TRUE);
                    $contact_info = Factory::getView('contacts','default',$ref);
                    $this->contact_info = $contact_info;
                }

                $this->info = $info;
            break;
        }

        $custom_fields = array('deal','company','person');
        if ( in_array($layout,$custom_fields) ) {
            $this->custom_fields = Factory::getView('print','custom_fields','phtml');
            $this->custom_fields->item_type = $layout;
            $this->custom_fields->item = $info[0];
        }

        $js = "jQuery(document).ready(function(){
                window.print();
        })";

        $document->addScriptDeclaration($js);

        //display
        return parent::render();
    }

}
