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

 class MailHelper
 {

    public static function loadStats($person_id)
    {

        //Load stats model
        $statsModel = new CobaltModelStats();
        $statsModel->set('person_id',$person_id);

        //Load view
        $coffeeView = CobaltHelperView::getView('emails','coffee.report');

        //Get Total Deals Amount
        $activeDeals = $statsModel->getActiveDealsAmount();
        $coffeeView->totalDealsAmount = $activeDeals;

        //Get Stage Details
        $stages = $statsModel->getStages();
        $coffeeView->stages = $stages;

        //Get Number of Converted Leads
        $leads = $statsModel->getLeads();
        $coffeeView->numConvertedLeads = $leads['contact'];

        //Get Number of New Leads
        $coffeeView->numNewLeads = $leads['lead'];

        //Get Note Details
        $notes = $statsModel->getNotes();
        $coffeeView->notes = $notes;

        //Get ToDo Details
        $todos = $statsModel->getTodos();
        $coffeeView->todos = $todos;

        //Get Deal Activity
        $dealActivity = $statsModel->getDealActivity();
        $coffeeView->dealActivity = $dealActivity;

        //Get Lead Activity
        $coffeeView->leadActivity = $leadActivity;

        //Get Contact Activity
        $coffeeView->contactActivity = $contactActivity;

        return $coffeeView;
    }

    public static function sendMail($layout,$recipient)
    {
        $mailer = JFactory::getMailer();
        $mailer->isHTML(true);
        $mailer->Encoding = 'base64';

        $config = JFactory::getConfig();
        $sender = array(
                    $config->getValue( 'config.mailfrom' ),
                       $config->getValue( 'config.fromname' )
                   );

        $mailer->setSender($sender);
        $mailer->addRecipient($recipient);

        $mailer->setSubject(TextHelper::_('COBALT_COFFEE_REPORT_SUBJECT').' '.CobaltHelperDate::formatDate(date('Y-m-d')));

        ob_start();

        $layout->display();
        $body = ob_get_contents();

        ob_end_clean();

        $mailer->setBody($body);
        $send = $mailer->Send();
        if ($send !== true) {
            echo 'Error sending email: ' . $send->message;
        }
    }

}
