<?php

namespace Cobalt;

defined('_CEXEC') or die;

use League\Di\Container as LeagueContainer;

class Container extends LeagueContainer
{
    protected static $instance;

    protected $aliases = array();

    protected $providers = array();

    public function alias($alias, $binding)
    {
        $this->aliases[$alias] = $binding;

        return $this;
    }

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

    public function resolve($binding)
    {
        if (isset($this->aliases[$binding])) {
            $binding = $this->aliases[$binding];
        }

        return parent::resolve($binding);
    }
}
