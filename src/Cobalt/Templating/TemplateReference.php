<?php
/**
 * Cobalt CRM
 *
 * @copyright  Copyright (C) 2012 - 2014 cobaltcrm.org All Rights Reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Cobalt\Templating;

use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * Internal representation of a template.
 *
 * @since  1.0
 */
class TemplateReference implements TemplateReferenceInterface
{
	/**
	 * Array containing the template params
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $parameters;

	/**
	 * Constructor
	 *
	 * @param   string  $name  The layout name to render
	 * @param   string  $view  The view the layout falls under
	 *
	 * @since   1.0
	 */
	public function __construct($name, $view)
	{
		$this->parameters = array(
			'name' => $name,
			'view' => $view,
		);
	}

	/**
	 * Returns the string representation as shortcut for getLogicalName().
	 *
	 * Alias of getLogicalName().
	 *
	 * @return  string  The template name
	 *
	 * @since   1.0
	 */
	public function __toString()
	{
		return $this->getLogicalName();
	}

	/**
	 * Sets a template parameter.
	 *
	 * @param   string  $name  The parameter name
	 * @param   string  $value The parameter value
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 * @throws  \InvalidArgumentException if the parameter name is not supported
	 */
	public function set($name, $value)
	{
		if (!array_key_exists($name, $this->parameters))
		{
			throw new \InvalidArgumentException(sprintf('The template does not support the "%s" parameter.', $name));
		}

		$this->parameters[$name] = $value;

		return $this;
	}

	/**
	 * Gets a template parameter.
	 *
	 * @param   string  $name  The parameter name
	 *
	 * @return  string  The parameter value
	 *
	 * @since   1.0
	 * @throws  \InvalidArgumentException if the parameter name is not supported
	 */
	public function get($name)
	{
		if (array_key_exists($name, $this->parameters))
		{
			return $this->parameters[$name];
		}

		throw new \InvalidArgumentException(sprintf('The template does not support the "%s" parameter.', $name));
	}

	/**
	 * Gets the template parameters.
	 *
	 * @return  array  An array of parameters
	 *
	 * @since   1.0
	 */
	public function all()
	{
		return $this->parameters;
	}

	/**
	 * Returns the path to the template.
	 *
	 * By default, it just returns the template name.
	 *
	 * @return  string  A path to the template or a resource
	 *
	 * @since   1.0
	 */
	public function getPath()
	{
		return $this->parameters['name'];
	}

	/**
	 * Returns the "logical" template name.
	 *
	 * The template name acts as a unique identifier for the template.
	 *
	 * @return  string  The template name
	 *
	 * @since   1.0
	 */
	public function getLogicalName()
	{
		return $this->parameters['name'];
	}
}
