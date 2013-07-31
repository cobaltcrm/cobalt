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

class TableConversation extends JTable
{
    /**
     * Constructor
     *
     * @param object Database connector object
     */
    public function __construct( &$db )
    {
        parent::__construct('#__conversations', 'id', $db);
    }
}
