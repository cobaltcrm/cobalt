<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

class crmInstall
{

	var $baseurl = null;

	function __construct(){

		//construct base url
		$this->baseurl = CURI::base();
		session_start();
		$this->error = array_key_exists('error',$_SESSION) ? $_SESSION['error'] : null;
	}


    /**
     * Method to install the component
     * 
     * @param  mixed    $parent     The class calling this method
     * @return void
     */
    function install() 
    {

        //include default installation view
        include_once("view/default.php");

    }

    /**
	 * Method to update the component
	 * 
	 * @param  mixed	$parent		The class calling this method
	 * @return void
	 */
	function update() 
	{
		$this->addNewFields();
		
		$this->updateVersion();
		
		echo '<p>' . JText::_('COM_COBALT_UPDATE_SUCCESSFULL') . '</p>';
	}

	/**
	 * Method to uninstall the component
	 * 
	 * @param  mixed	$parent		The class calling this method
	 * @return void
	 */
	function uninstall()
	{
		echo '<p>' . JText::_('COM_COBALT_UNINSTALL_SUCCESSFULL') . '</p>';
	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @param  mixed	$parent		The class calling this method
	 * @return void
	 */
	function preflight($type, $parent) 
	{
		
	}
 
	/**
	 * method to run after an install/update/uninstall method
	 * 
	 * @param  mixed	$parent		The class calling this method
	 * @return void
	 */
	function postflight($type, $parent) 
	{
		
	}

	function addNewFields(){


	}

	function alterTable($table, $fields)
	{
		$db =& JFactory::getDBO();
		
		$table = $db->nameQuote($table);
		
		// Get the existing fields
		$db->setQuery("SHOW FIELDS FROM $table");
		$cols = $db->loadObjectList();
		$existingFields = array();
		for ($i=0, $n=count($cols); $i<$n; $i++) {
			$existingFields[] = $cols[$i]->Field;
		}
		
		// Determine which fields are missing from the table
		$toAdd = array();
		foreach ($fields AS $field=>$type) {
			if (! in_array($field, $existingFields)) {
				$toAdd[] = $db->nameQuote($field)." ".$type;
			}
		}
		
		// Add the missing fields to the table
		if (count($toAdd) > 0) {
			$newColumns = "ADD COLUMN ".implode(", ADD COLUMN ", $toAdd);
			$query = "ALTER TABLE $table $newColumns";
			$db->setQuery($query);
			$db->query();
		}

	}

	function updateVersion(){



	}

      
} 
    
?>