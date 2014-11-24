<?php
/**
 * Part of the Joomla Tracker Router Package
 *
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Cobalt;

use Joomla\Controller\ControllerInterface;
use Joomla\Input\Input;
use Joomla\Router\Router as JoomlaRouter;

use Cobalt\Helper\RouteHelper;

/**
 * Joomla! Tracker Router
 *
 * @since  1.0
 */
class Router extends JoomlaRouter
{
    /**
     * Get a ControllerInterface object for a given name.
     *
     * @param string $name The controller name (excluding prefix) for which to fetch and instance.
     *
     * @return ControllerInterface
     *
     * @since   1.0
     * @throws \RuntimeException
     */
    protected function fetchController($name)
    {
        // Derive the controller class name.
        $class = $this->controllerPrefix . ucfirst($name);

        // Check for the requested controller.
        if ($class === $this->default || !class_exists($class) || !is_subclass_of($class, 'Joomla\\Controller\\ControllerInterface')) {
            // See if there's an action class in the libraries if we aren't calling the default task
            $task = $this->input->getCmd('task');

            if ($task && $task != 'default') {
                $class = 'Cobalt\\Controller\\' . ucfirst($task);
            }

            if (!class_exists($class) || !is_subclass_of($class, 'Joomla\\Controller\\ControllerInterface')) {
                // Look for a default controller for the component
                $class = 'Cobalt\\Controller\\DefaultController';

                if (!class_exists($class) || !is_subclass_of($class, 'Joomla\\Controller\\ControllerInterface')) {
                    // Nothing found. Panic.
                    throw new \RuntimeException(sprintf('Controller not found for %s task', $task));
                }
            }
        }

        // Instantiate the controller.
        return new $class($this->input);
    }

    public static function to($url)
    {
        return RouteHelper::_(Factory::getApplication()->getRouter()->getRouteFor($url));
    }

    public function getRouteFor($url)
    {
        $parts = parse_url($url);

        if (empty($parts['query'])) {
            return $url;
        }

        parse_str($parts['query'], $query);

        $array = array();

        if (isset($query['view'])) {
            $view = $query['view'];
            $array[] = $view;
            unset($query['view']);
        }

        if (isset($query['layout'])) {
            $array[] = $query['layout'];
            unset($query['layout']);
        }

        if (isset($query['id'])) {
            $array[] = $query['id'];
            unset($query['id']);
        }

        if (empty($array)) {
            return $url;
        }

        if (!empty($query)) {
            return '/' . implode('/', $array) . '?' . http_build_query($query);
        }

        return '/' . implode('/', $array);
    }
}
