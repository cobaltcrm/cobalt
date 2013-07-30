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

jimport( 'joomla.plugin.plugin' );

/**
 * Joomla! System Remember Me Plugin
 *
 * @package		Joomla
 * @subpackage	System
 */
class plgSystemCobaltform extends JPlugin
{

    public function onAfterRender()
    {

        $mainframe = JFactory::getApplication();
        if (!$mainframe->isAdmin()) {
            $buffer = JResponse::getBody();

            $regex  = '#\[cobaltform([0-9]*)\]#';
            $buffer = preg_replace_callback($regex, array('plgSystemCobaltform', 'loadForm'), $buffer);

            JResponse::setBody($buffer);
        }

        return true;
    }

    public function loadForm(&$matches)
    {
        $db = JFactory::getDBO();

        $query = $db->getQuery(true);
        $query->select('html')->from('#__formwizard')->where('id='.$matches[1]);
        $db->setQuery($query);
        $html = $db->loadResult();

        return $html;
    }
}
