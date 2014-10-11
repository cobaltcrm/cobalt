<?php
/**
 * Cobalt CRM
 *
 * @copyright  Copyright (C) 2012 - 2014 cobaltcrm.org All Rights Reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Cobalt\Provider;

use Cobalt\Templating\Helper\AssetsHelper;
use Cobalt\Templating\PhpEngineRenderer;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

use Symfony\Component\Templating\Asset\UrlPackage;
use Symfony\Component\Templating\Helper\SlotsHelper;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParser;

/**
 * PhpEngine renderer service provider
 *
 * @since  1.0
 */
class PhpengineRendererProvider implements ServiceProviderInterface
{
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function register(Container $container)
	{
		$container->set(
			'BabDev\Renderer\RendererInterface',
			function (Container $container) {
				/* @type  \Cobalt\Application  $app */
				$app = $container->get('app');

				$loader = new FilesystemLoader(array(JPATH_THEMES . '/' . $app->getTemplate() . '/%name%.php'));

				$engine = new PhpEngine(new TemplateNameParser, $loader);
				$engine->addHelpers(array(new SlotsHelper, new AssetsHelper(new UrlPackage($app->get('uri.base.full')))));

				return new PhpEngineRenderer(new TemplateNameParser, $loader, $engine);
			},
			true,
			true
		);

		$container->alias('renderer', 'BabDev\Renderer\RendererInterface');

		return;
	}
}
