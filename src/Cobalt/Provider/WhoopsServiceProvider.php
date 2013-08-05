<?php
/**
 * @package    Cobalt.CRM
 *
 * @copyright  Copyright (C) 2013 Webspark, LLC. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Cobalt\Provider;

use Cobalt\Container;
use Joomla\Registry\Registry;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

class WhoopsServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        /** @var $config Registry */
        $config = $container->resolve('config');

        if ($config->get('debug', false)) {
            $whoops = new Run;
            $handler = new PrettyPageHandler;

            $editor = $config->get('debugEditor');

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