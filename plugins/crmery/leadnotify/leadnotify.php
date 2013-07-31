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

jimport( 'joomla.plugin.plugin' );

/**
 * Joomla! System Remember Me Plugin
 *
 * @package		Joomla
 * @subpackage	System
 */
class plgCobaltLeadNotify extends JPlugin
{

    public function onAfterPersonSave(&$row)
    {
        if ($row->status=='created' && $row->form_id!='' && $row->owner_id>0) {
            $app = JFactory::getApplication();
            $db = JFactory::getDBO();

            $query = $db->getQuery(true);

            $query->select('fields')
                    ->from('#__formwizard')
                    ->where('id='.$row->form_id);
            $db->setQuery($query);
            $fields = $db->loadResult();
            $fields = unserialize($fields);

            $query->clear();
            $query->select('u.email')
                    ->from('#__users AS u')
                    ->where('cu.id = '.$row->owner_id);
            $db->setQuery($query);
            $to = $db->loadResult();

            $from		= array($app->getCfg('mailfrom'), $app->getCfg('fromname'));
            $subject 	= $this->params->get('subject');
            $body 		= $this->params->get('pretext');

            $body	   .= "<br /><br />";

            if (count($fields) > 0) {
                foreach ($fields as $field) {
                    $body .= $row->$field."<br />";
                }
            }

            # Invoke JMail Class
            $mailer = JFactory::getMailer();

            # Set sender array
            $mailer->setSender($from);

            # Add a recipient
            $mailer->addRecipient($to);

            $mailer->setSubject($subject);
            $mailer->setBody($body);

            $mailer->isHTML();

            $mailer->send();
        }

    return true;

    }

}
