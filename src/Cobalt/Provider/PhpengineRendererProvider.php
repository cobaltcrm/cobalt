<?php
/**
 * Cobalt CRM
 *
 * @copyright  Copyright (C) 2012 - 2014 cobaltcrm.org All Rights Reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Cobalt\Provider;

use BabDev\Renderer\PhpEngineRenderer;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

use Symfony\Component\Templating\Loader\FilesystemLoader;
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

				$loader = new FilesystemLoader(array(JPATH_THEMES . '/' . $app->getTemplate()));

				return new PhpEngineRenderer(new TemplateNameParser, $loader);
			},
			true,
			true
		);

		$container->alias('renderer', 'BabDev\Renderer\RendererInterface');

		return;
	}
}
