<?php

namespace Cobalt;

defined('_CEXEC') or die;

use Joomla\DI\Container as JoomlaContainer;

class Container extends JoomlaContainer
{
    protected static $instance;

    /**
     * @return Container
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static;
        }

        return self::$instance;
    }

    public static function fetch($key)
    {
        return self::getInstance()->get($key);
    }
}
