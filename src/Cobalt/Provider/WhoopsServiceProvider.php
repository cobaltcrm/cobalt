<?php
/**
 * @package    Cobalt.CRM
 *
 * @copyright  Copyright (C) 2013 Webspark, LLC. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Cobalt\Provider;

use Cobalt\Container;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

class WhoopsServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        /** @var $config \JConfig */
        $config = $container->resolve('config');

        if ($config->debug) {
            $whoops = new Run;
            $handler = new PrettyPageHandler;

            $editor = $config->debugEditor;

            if ($editor == 'pstorm') {
                $handler->setEditor(function ($file, $line) {
                        return "pstorm://$file:$line";
                    });
            } else {
                $handler->setEditor($editor);
            }

            $whoops->pushHandler($handler);

            $whoops->register();

            $container->bind('whoops', function() use ($whoops) {
                    return $whoops;
                });
        }
    }
}