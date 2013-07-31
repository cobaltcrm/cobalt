<?php

namespace Cobalt;

defined('_CEXEC') or die;

use League\Di\Container as LeagueContainer;

class Container extends LeagueContainer
{
    protected static $instance;

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new LeagueContainer;
        }

        return self::$instance;
    }

    public static function get($key)
    {
        return self::getInstance()->resolve($key);
    }
}
