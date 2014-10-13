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

class Toolbar
{
	protected $buttons;

	public function __construct()
	{
		$this->buttons = array();
		$this->app = Factory::getApplication();
	}

	/**
     * Adds the common 'new' link to the button bar.
     *
     * @param string    $view   Name of the view which should load the form.
     * @param string  	$label  Text of the link.
     * @param string  	$class  An override for the CSS class.
     * @param string 	$icon   Part of the icon class name.
     * @param array 	$attr   Array of attributes
     *
     * @return void
     *
     * @since   1.0
     */
    public function addNew($view = null, $label = 'COBALT_TOOLBAR_NEW', $class = 'btn btn-primary', $icon = 'plus')
    {
        parse_str($view, $uri);
        if (count($uri) == 1) {
            $uri = array('view' => $view, 'layout' => 'edit');
        } else {
            // get keys
            $keys = array_keys($uri);
            // cleanup empty values
            $uri = array_filter($uri);
            // merge view name with data
            $uri = array_merge(array('view' => $keys[0]),$uri);
        }
        $this->add($uri,$label, $class, $icon);
    }

    /**
     * Generic Link to button bar
     *
     * @param array  	$uri 	Array to build link
     * @param string  	$label  Text of the link.
     * @param string  	$class  An override for the CSS class.
     * @param string  	$icon 	Part of the icon class name.
     * @param array  	$attr 	Array of attributes to set on link
     *
     * @return void
     *
     * @since   1.0
     */
    public function add(array $uri = array(), $label, $class, $icon, array $attr = array())
    {
        if (empty($uri)) {
            $uri = array(
                'view' => $this->app->input->getCmd('view'),
                'layout' => $this->app->input->getCmd('layout','edit')
            );
        }

        $link = new Button('a', $label, '', '', $class);

        $attributes = array();
        foreach ($attr as $name => $value) {
            $attributes[] = sprintf('%s="%s"',$name,$value);
        }
        $link->setAttribute(implode(' ',$attributes));

        $link->setLink('index.php?'.http_build_query($uri))->setIcon($icon);
        $this->buttons[] = $link;
    }

    /**
     * Adds the common 'trash row' button to the button bar.
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
    public function addTrashRow($label = 'COBALT_TOOLBAR_TRASH', $class = 'btn btn-default', $icon = 'minus')
    {
    	$view = $this->app->input->getCmd('view');
    	$link = new Button('a', $label, '', '', $class);
    	$link->setLink('#')->setIcon($icon)->setAttribute('onclick="Cobalt.deleteListItems()"');
        $this->buttons[] = $link;
    }

    /**
     * Adds the common 'delete row' button to the button bar.
     * This delete button is intended to delete rows from a table.
     *
     * @param string    $label  Text of the link.
     * @param string    $class  An override for the CSS class.
     * @param string    $icon   Part of the icon class name.
     *
     * @return void
     *
     * @since   1.0
     */
    public function addDeleteRow($label = 'COBALT_TOOLBAR_DELETE', $class = 'btn btn-default', $icon = 'minus')
    {
        $view = $this->app->input->getCmd('view');
        $link = new Button('a', $label, '', '', $class);
        $link->setLink('#')->setIcon($icon)->setAttribute('onclick="Cobalt.deleteListItems(\'delete\')"');
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
     * @param string    $view  Name of the view where should be user returned.
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
