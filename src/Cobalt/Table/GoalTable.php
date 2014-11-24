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

use Cobalt\Helper\DateHelper;

use Joomla\Database\DatabaseDriver;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class GoalTable extends AbstractTable
{
	/**
	 * Constructor
	 *
	 * @param   DatabaseDriver  $db  A database connector object
	 *
	 * @since   1.0
	 */
	public function __construct(DatabaseDriver $db)
	{
		parent::__construct('#__goals', 'id', $db);
	}

	/**
	 * Method to bind an associative array or object to the AbstractTable instance.
	 *
	 * This method only binds properties that are publicly accessible and optionally takes an array of properties to ignore when binding.
	 *
	 * @param   array|\stdClass  $src     An associative array or object to bind to the AbstractTable instance.
	 * @param   array|string     $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 * @throws  \InvalidArgumentException
	 */
	public function bind($src, $ignore = array())
    {
	    //transform date to SQL
	    if (!empty($src['start_date']))
	    {
            $src['start_date'] = $this->dateToSql($array['start_date']);
	    }

	    if (!empty($src['end_date']))
	    {
            $src['end_date'] = $this->dateToSql($array['end_date']);
	    }

	    return parent::bind($src, $ignore);
    }

    private function dateToSql($date)
    {
	    return DateHelper::formatDBDate(str_replace('/', '-', $date));
    }
}
