<?php
/**
 * Cobalt CRM
 *
 * @copyright  Copyright (C) 2012 - 2014 cobaltcrm.org All Rights Reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Cobalt\Templating\Helper;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Templating\Helper\CoreAssetsHelper as BaseAssetsHelper;

/**
 * AssetsHelper
 *
 * @since  1.0
 */
class AssetsHelper extends BaseAssetsHelper
{
	/**
	 * Container holding all assets
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $assets = array();

	/**
	 * Adds a JS script to the template
	 *
	 * @param   string  $script    The script to add
	 * @param   string  $location  The location to render the script at in the template
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function addScript($script, $location = 'head')
	{
		$assets     =& $this->assets;
		$addScripts = function ($s) use ($location, &$assets)
		{
			if ($location == 'head')
			{
				// Special place for these so that declarations and scripts can be mingled
				$assets['headDeclarations'][] = array(
					'type' => 'script',
					'src'  => $s
				);
			}
			else
			{
				if (!isset($assets['scripts'][$location]))
				{
					$assets['scripts'][$location] = array();
				}

				if (!in_array($s, $assets['scripts'][$location]))
				{
					$assets['scripts'][$location][] = $s;
				}
			}
		};

		if (is_array($script))
		{
			foreach ($script as $s)
			{
				$addScripts($s);
			}
		}
		else
		{
			$addScripts($script);
		}
	}

	/**
	 * Adds a JS script declaration to the template
	 *
	 * @param   string  $script    The script to add
	 * @param   string  $location  The location to render the script at in the template
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function addScriptDeclaration($script, $location = 'head')
	{
		if ($location == 'head')
		{
			// Special place for these so that declarations and scripts can be mingled
			$this->assets['headDeclarations'][] = array(
				'type'   => 'declaration',
				'script' => $script
			);
		}
		else
		{
			if (!isset($this->assets['scriptDeclarations'][$location]))
			{
				$this->assets['scriptDeclarations'][$location] = array();
			}

			if (!in_array($script, $this->assets['scriptDeclarations'][$location]))
			{
				$this->assets['scriptDeclarations'][$location][] = $script;
			}
		}
	}

	/**
	 * Adds a stylesheet to be loaded in the template header
	 *
	 * @param   string  $stylesheet  The stylesheet to add
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function addStylesheet($stylesheet)
	{
		$assets   =& $this->assets;
		$addSheet = function ($s) use (&$assets)
		{
			if (!isset($assets['stylesheets']))
			{
				$assets['stylesheets'] = array();
			}

			if (!in_array($s, $assets['stylesheets']))
			{
				$assets['stylesheets'][] = $s;
			}
		};

		if (is_array($stylesheet))
		{
			foreach ($stylesheet as $s)
			{
				$addSheet($s);
			}
		}
		else
		{
			$addSheet($stylesheet);
		}
	}

	/**
	 * Add style tag to the header
	 *
	 * @param   string  $styles  The style declaration to add
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function addStyleDeclaration($styles)
	{
		if (!isset($this->assets['styleDeclarations']))
		{
			$this->assets['styleDeclarations'] = array();
		}

		if (!in_array($styles, $this->assets['styleDeclarations']))
		{
			$this->assets['styleDeclarations'][] = $styles;
		}
	}

	/**
	 * Adds a custom declaration to the header
	 *
	 * @param   string  $declaration  The declaration to add
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function addCustomDeclaration($declaration, $location = 'head')
	{
		if ($location == 'head')
		{
			$this->assets['headDeclarations'][] = array(
				'type'        => 'custom',
				'declaration' => $declaration
			);
		}
		else
		{
			if (!isset($this->assets['customDeclarations'][$location]))
			{
				$this->assets['customDeclarations'][$location] = array();
			}

			if (!in_array($declaration, $this->assets['customDeclarations'][$location]))
			{
				$this->assets['customDeclarations'][$location][] = $declaration;
			}
		}
	}

	/**
	 * Outputs the stylesheets and style declarations
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function outputStyles()
	{
		if (isset($this->assets['stylesheets']))
		{
			foreach ($this->assets['stylesheets'] as $s)
			{
				echo '<link rel="stylesheet" href="' . $this->getUrl($s) . '" />' . "\n";
			}
		}

		if (isset($this->assets['styleDeclarations']))
		{
			echo "<style>\n";

			foreach ($this->assets['styleDeclarations'] as $d)
			{
				echo "$d\n";
			}

			echo "</style>\n";
		}
	}

	/**
	 * Outputs the script files and declarations
	 *
	 * @param   string  $location  The location to render scripts for
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function outputScripts($location)
	{
		if (isset($this->assets['scripts'][$location]))
		{
			foreach ($this->assets['scripts'][$location] as $s)
			{
				echo '<script src="' . $this->getUrl($s) . '"></script>' . "\n";
			}
		}

		if (isset($this->assets['scriptDeclarations'][$location]))
		{
			echo "<script>\n";

			foreach ($this->assets['scriptDeclarations'][$location] as $d)
			{
				echo "$d\n";
			}

			echo "</script>\n";
		}

		if (isset($this->assets['customDeclarations'][$location]))
		{
			foreach ($this->assets['customDeclarations'][$location] as $d)
			{
				echo "$d\n";
			}
		}
	}

	/**
	 * Output head scripts, stylesheets, and custom declarations
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function outputHeadDeclarations()
	{
		$this->outputStyles();

		if (isset($this->assets['headDeclarations']))
		{
			foreach ($this->assets['headDeclarations'] as $h)
			{
				if ($h['type'] == 'script')
				{
					echo '<script src="' . $this->getUrl($h['src']) . '"></script>' . "\n";
				}
				elseif ($h['type'] == 'declaration')
				{
					echo "<script>\n{$h['script']}\n</script>\n";
				}
				else
				{
					echo $h['declaration'] . "\n";
				}
			}
		}
	}

	/**
	 * Returns the canonical name of this helper
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function getName()
	{
		return 'assets';
	}
}
