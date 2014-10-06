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

use Cobalt\Helper\Button;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Toolbar
{
	protected $buttons;

	public function __construct()
	{
		$this->buttons = array();
		$this->app = \Cobalt\Container::fetch('app');
	}

	/**
     * Adds the common 'new' link to the button bar.
     *
     * @param string  	$label  Text of the link.
     * @param string  	$class  An override for the CSS class.
     * @param string 	$icon 	Part of the icon class name.
     *
     * @return void
     *
     * @since   1.0
     */
    public function addNew($label = 'COBALT_TOOLBAR_NEW', $class = 'btn btn-primary', $icon = 'plus')
    {
    	$view = $this->app->input->getCmd('view');
    	$link = new Button('a', $label, '', '', $class);
    	$link->setLink('index.php?view=' . $view . '&layout=edit')->setIcon($icon);
        $this->buttons[] = $link;
    }

    /**
     * Adds the common 'delete row' button to the button bar.
     * This delete button is intended to delete rows from a table.
     *
     * @param string  	$label  Text of the link.
     * @param string  	$class  An override for the CSS class.
     * @param string 	$icon 	Part of the icon class name.
     *
     * @return void
     *
     * @since   1.0
     */
    public function addDeleteRow($label = 'COBALT_TOOLBAR_DELETE', $class = 'btn btn-default', $icon = 'minus')
    {
    	$view = $this->app->input->getCmd('view');
    	$link = new Button('a', $label, '', '', $class);
    	$link->setLink('#')->setIcon($icon)->setAttribute('onclick="Cobalt.deleteListItems()"');
        $this->buttons[] = $link;
    }

    /**
     * Adds the common 'save' button to the button bar.
     *
     * @param string  	$label  Text of the link.
     * @param string  	$class  An override for the CSS class.
     * @param string 	$icon 	Part of the icon class name.
     *
     * @return void
     *
     * @since   1.0
     */
    public function save($label = 'COBALT_TOOLBAR_SAVE', $class = 'btn btn-primary', $icon = 'floppy-disk', $name = 'task', $value = 'save')
    {
    	$view = $this->app->input->getCmd('view');
    	$link = new Button('button', $label, $name, $value, $class);
    	$link->setIcon($icon);
        $this->buttons[] = $link;
    }

    /**
     * Adds the common 'new' link to the button bar.
     *
     * @param string  	$label  Text of the link.
     * @param string  	$class  An override for the CSS class.
     * @param string 	$icon 	Part of the icon class name.
     *
     * @return void
     *
     * @since   1.0
     */
    public function cancel($view = null, $label = 'COBALT_TOOLBAR_CANCEL', $class = 'btn btn-default', $icon = 'floppy-remove')
    {
        if (!$view)
        {
            $view = $this->app->input->getCmd('view');
        }
    	
    	$link = new Button('a', $label, '', '', $class);
    	$link->setLink(RouteHelper::_('index.php?view=' . $view))->setIcon($icon);
        $this->buttons[] = $link;
    }

    /**
     * Renders HTML of the toolbar.
     *
     * @return string
     *
     * @since   1.0
     */
    public function render()
    {
    	$toolbar = '<div class="pull-right">';

    	foreach ($this->buttons as $button)
    	{
    		$toolbar .= $button->render() . "\n";
    	}

        $toolbar .= '</div>';

        return $toolbar;
    }
}
