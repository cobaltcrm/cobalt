<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\Model;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Menu extends DefaultModel
{
    public function store()
    {
        //Load Tables
        $row = $this->getTable('Menu');
        $data = $this->app->input->post->getArray();

        //date generation
        $date = date('Y-m-d H:i:s');
        $data['modified'] = $date;

        //serialize menu items for storage
        $data['menu_items'] = serialize($data['menu_items']);

        // Bind the form fields to the table
	    try
	    {
		    $row->save($data);
	    }
	    catch (\Exception $exception)
	    {
		    $this->app->enqueueMessage($exception->getMessage(), 'error');

		    return false;
	    }

        return true;
    }

    public function getMenu()
    {
        $db = $this->getDb();
        $query = $db->getQuery(true);

        $query->select("*")->from("#__menu")->where("id=1");
        $db->setQuery($query);

        $menu = $db->loadObject();
        $menu->menu_items = unserialize($menu->menu_items);

        return $menu;

    }

    public function getMenuTemplate()
    {
        return array('dashboard','deals','people','companies','calendar','documents','goals','reports');
    }

}
