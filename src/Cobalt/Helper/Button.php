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

use Cobalt\Helper\TextHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Button
{
	/**
	 * a / input / button
	 * string
	 */
	protected $tag;

	/**
	 * Can be submit (default) or button
	 * string
	 */
	protected $type;

	/**
	 * Text of the button
	 * string
	 */
	protected $label;

	/**
	 * Value saved with the form
	 * string
	 */
	protected $value;

	/**
	 * Name saved with the form
	 * string
	 */
	protected $name;

	/**
	 * CSS class
	 * string
	 */
	protected $class;

	/**
	 * ID of the button
	 * string
	 */
	protected $id;

	/**
	 * URL for link
	 * string
	 */
	protected $link;

	/**
	 * glyphicon class identifier. 
	 * just 'search' for 'glyphicon glyphicon-search'
	 * string
	 */
	protected $icon;

	/**
	 * Any other additional attribute
	 * string
	 */
	protected $attribute;

	public function __construct($tag = 'button', $label = '', $name = '', $value = '', $class = 'btn btn-default', $id = '', $type = 'submit', $attribute = '')
	{
		$this->tag 		= $tag;
		$this->label 	= $label;
		$this->type 	= $type;
		$this->value 	= $value;
		$this->name 	= $name;
		$this->class 	= $class;
		$this->id 		= $id;
		$this->icon 	= '';
	}

	public function setLink($link)
	{
		$this->link = $link;
		return $this;
	}

	public function setIcon($icon)
	{
		$this->icon = $icon;
		return $this;
	}

	public function setAttribute($attribute)
	{
		$this->attribute = $attribute;
		return $this;
	}

	public function getIconHtml()
	{
		if ($this->icon)
		{
			return '<span class="glyphicon glyphicon-' . $this->icon . '"></span> ';
		}
		
		return '';
	}

	public function render()
	{
		if ($this->tag == 'button')
		{
			return '<button name="' . $this->name . '" type="' . $this->type . '" id="' . $this->id . '" value="' . $this->value . '" class="' . $this->class . '" ' . $this->attribute . '>' . $this->getIconHtml() . TextHelper::_($this->label) . '</button>';
		}

		if ($this->tag == 'input')
		{
			return '<input name="' . $this->name . '" type="' . $this->type . '" id="' . $this->id . '" value="' . $this->getIconHtml() . TextHelper::_($this->label) . '" class="' . $this->class . '" ' . $this->attribute . ' />';
		}
		
		if ($this->tag == 'a')
		{
			return '<a href="' . $this->link . '" id="' . $this->id . '" class="' . $this->class . '" ' . $this->attribute . '>' . $this->getIconHtml() . TextHelper::_($this->label) . '</a>';
		}
	}
}
