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

use Cobalt\Factory;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

 class MailHelper
 {

    public static function loadStats($person_id)
    {

        //Load stats model
	    /** @var \Cobalt\Model\Stats $statsModel */
        $statsModel = Factory::getModel('Stats');
        $statsModel->set('person_id',$person_id);
        $leads = $statsModel->getLeads();

	    $viewVars = array(
		    'totalDealsAmount' => $statsModel->getActiveDealsAmount(),
	        'stages'           => $statsModel->getStages(),
	        'numConvertedLeads' => $leads['contact'],
	        'numNewLeads'       => $leads['lead'],
	        'notes'             => $statsModel->getNotes(),
	        'todos'             => $statsModel->getTodos(),
	        'dealActivity'      => $statsModel->getDealActivity()
	    );

        //Load view
        $coffeeView = Factory::getView('emails','coffee.report', 'html', $viewVars, $statsModel);

        return $coffeeView;
    }

    public static function sendMail($layout,$recipient)
    {
        $mailer = \JFactory::getMailer();
        $mailer->isHTML(true);
        $mailer->Encoding = 'base64';

        $config = Factory::getApplication()->getContainer()->get('config');
        $sender = array(
                    $config->get( 'mailfrom' ),
                       $config->get( 'fromname' )
                   );

        $mailer->setSender($sender);
        $mailer->addRecipient($recipient);

        $mailer->setSubject(TextHelper::_('COBALT_COFFEE_REPORT_SUBJECT').' '.DateHelper::formatDate(date('Y-m-d')));

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
