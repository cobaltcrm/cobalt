<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\Table;

use JTable;
use JFactory;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class AbstractTable extends JTable
{
    protected $_tbl_key = 'id';

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    public function __construct()
    {
        if (empty($this->_tbl)) {
            throw new \InvalidArgumentException('The $_tbl key has not been set in ' . get_class($this));
        }

        $db = JFactory::getDbo();
        parent::__construct($this->_tbl, $this->_tbl_key, $db);
    }
}
