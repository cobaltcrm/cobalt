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
    protected $parameters;

    public function __construct($name, $view)
    {
        $this->parameters = array(
            'name' => $name,
            'view' => $view,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getLogicalName();
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function set($name, $value)
    {
        if (array_key_exists($name, $this->parameters)) {
            $this->parameters[$name] = $value;
        } else {
            throw new \InvalidArgumentException(sprintf('The template does not support the "%s" parameter.', $name));
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function get($name)
    {
        if (array_key_exists($name, $this->parameters)) {
            return $this->parameters[$name];
        }

        throw new \InvalidArgumentException(sprintf('The template does not support the "%s" parameter.', $name));
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function all()
    {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function getPath()
    {
        return $this->parameters['name'];
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function getLogicalName()
    {
        return $this->parameters['name'];
    }
}
