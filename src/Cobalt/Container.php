<?php

namespace Cobalt;

defined('_CEXEC') or die;

use League\Di\Container as LeagueContainer;

class Container extends LeagueContainer
{
    protected static $instance;

    protected $providers = array();

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

    public static function get($key)
    {
        return self::getInstance()->resolve($key);
    }

    public function registerServiceProvider(Provider\ServiceProviderInterface $provider)
    {
        $this->providers[] = $provider;

        return $this;
    }

    public function registerProviders()
    {
        foreach ($this->providers as $provider) {
            /** @var \Cobalt\Provider\ServiceProviderInterface $provider */
            $provider->register($this);
        }
    }
}
