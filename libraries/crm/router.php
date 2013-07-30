<?php
/**
 * @package		Joomla.Site
 * @subpackage	Application
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('JPATH_BASE') or die;

use Joomla\Router\RestRouter;

/**
 * Set the available masks for the routing mode
 */
const JROUTER_MODE_RAW = 0;

/**
 * Class to create and parse routes for the site application
 *
 * @package		Joomla.Site
 * @subpackage	Application
 * @since		1.5
 */
class CobaltRouter extends RestRouter
{
    /**
     * The rewrite mode
     *
     * @var    integer
     * @since  11.1
     */
    protected $mode = null;

    /**
     * The rewrite mode
     *
     * @var    integer
     * @since  11.1
     * @deprecated use $mode declare as private
     */
    protected $_mode = null;

    /**
     * An array of variables
     *
     * @var     array
     * @since   11.1
     */
    protected $vars = array();

    /**
     * An array of variables
     *
     * @var     array
     * @since   11.1
     * @deprecated use $vars declare as private
     */
    protected $_vars = array();

    /**
     * An array of rules
     *
     * @var    array
     * @since  11.1
     */
    protected $rules = array(
        'build' => array(),
        'parse' => array()
    );

    /**
     * An array of rules
     *
     * @var    array
     * @since  11.1
     * @deprecated use $rules declare as private
     */
    protected $_rules = array(
        'build' => array(),
        'parse' => array()
    );

    /**
     * @var    array  JRouter instances container.
     * @since  11.3
     */
    protected static $instances = array();

    public function __construct($options = array())
    {
        $this->_vars = array();

        if (array_key_exists('mode', $options)) {
            $this->_mode = $options['mode'];
        } else {
            $this->_mode = JROUTER_MODE_RAW;
        }
    }

    /**
     * Parse the URI
     *
     * @param	object	The URI
     *
     * @return array
     */
    public function parse(&$uri)
    {
        $vars = array();

        // Get the application
        $app = JFactory::getApplication('');

        if ($app->getCfg('force_ssl') == 2 && strtolower($uri->getScheme()) != 'https') {
            //forward to https
            $uri->setScheme('https');
            $app->redirect((string) $uri);
        }

        // Get the path
        $path = $uri->getPath();

        // Remove the base URI path.
        $path = substr_replace($path, '', 0, strlen(JURI::base(true)));

        // Check to see if a request to a specific entry point has been made.
        if (preg_match("#.*?\.php#u", $path, $matches)) {

            // Get the current entry point path relative to the site path.
            $scriptPath = realpath($_SERVER['SCRIPT_FILENAME'] ? $_SERVER['SCRIPT_FILENAME'] : str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']));
            $relativeScriptPath = str_replace('\\', '/', str_replace(JPATH_SITE, '', $scriptPath));

            // If a php file has been found in the request path, check to see if it is a valid file.
            // Also verify that it represents the same file from the server variable for entry script.
            if (file_exists(JPATH_SITE.$matches[0]) && ($matches[0] == $relativeScriptPath)) {

                // Remove the entry point segments from the request path for proper routing.
                $path = str_replace($matches[0], '', $path);
            }
        }

        //Remove the suffix
        if ($this->_mode == JROUTER_MODE_SEF) {
            if ($app->getCfg('sef_suffix') && !(substr($path, -9) == 'index.php' || substr($path, -1) == '/')) {
                if ($suffix = pathinfo($path, PATHINFO_EXTENSION)) {
                    $path = str_replace('.'.$suffix, '', $path);
                    $vars['format'] = $suffix;
                }
            }
        }

        //Set the route
        $uri->setPath(trim($path , '/'));
        $vars += $this->_parse($uri);
        $route = $this->CobaltParseRoute($vars);

        return $route;
    }

    public function _parse(&$uri)
    {
        $vars = array();

        // Process the parsed variables based on custom defined rules
        $vars = $this->_processParseRules($uri);

        // Parse RAW URL
        if ($this->_mode == JROUTER_MODE_RAW) {
            $vars += $this->_parseRawRoute($uri);
        }

        // Parse SEF URL
        if ($this->_mode == JROUTER_MODE_SEF) {
            $vars += $this->_parseSefRoute($uri);
        }

        return $vars;

    }

    public function build($url)
    {

        $uri = JUri::getInstance($url);

        $vars = array();

        // Process the parsed variables based on custom defined rules
        $vars = $this->_processParseRules($uri);

        // Parse RAW URL
        if ($this->_mode == JROUTER_MODE_RAW) {
            $vars += $this->_buildRawRoute($uri);
        }

        // Parse SEF URL
        if ($this->_mode == JROUTER_MODE_SEF) {
            $vars += $this->_buildSefRoute($uri);
        }

        // Get the path data
        $route = $uri->getPath();

        //Add the suffix to the uri //$this->_mode == JROUTER_MODE_SEF &&
        if ($route) {
            $app = JFactory::getApplication();

            if ($app->getCfg('sef_suffix') && !(substr($route, -9) == 'index.php' || substr($route, -1) == '/')) {
                if ($format = $uri->getVar('format', 'html')) {
                    $route .= '.'.$format;
                    $uri->delVar('format');
                }
            }

            if ($app->getCfg('sef_rewrite')) {
                //Transform the route
                if ($route == 'index.php') {
                    $route = '';
                } else {
                    $route = str_replace('index.php/', '', $route);
                }
            }
        }

        //Add basepath to the uri
        $uri->setPath(JURI::base(true).'/'.$route.implode("/",$this->getVars()));

        // this needs to handle any remaining parts of the query string that were not modified
        $uri->setQuery("");

        return $uri;
    }

    protected function _parseRawRoute(&$uri)
    {

    }

    protected function _parseSefRoute(&$uri)
    {
        // Get the route
        $route = $uri->getPath();

        // Get the query data
        $query  = $uri->getQuery(true);
        $app	= JFactory::getApplication();

        // Use the component routing handler if it exists
        $path = JPATH_SITE . '/libraries/crm/router.php';
        $tmp = '';

        if ($tmp) {
            $route .= '/'.$tmp;
        } elseif ($route=='index.php') {
            $route = '';
        }

        //Set query again in the URI
        $uri->setQuery($query);
        $uri->setPath($route);

        $vars = array_merge($query,explode("/",$uri->getPath()));

        return $vars;
    }

    /**
     * Set a router variable, creating it if it doesn't exist
     *
     * @param string  $key    The name of the variable
     * @param mixed   $value  The value of the variable
     * @param boolean $create If True, the variable will be created if it doesn't exist yet
     *
     * @return void
     *
     * @since   11.1
     */
    public function setVar($key, $value, $create = true)
    {
        if ($create || array_key_exists($key, $this->_vars)) {
            $this->_vars[$key] = $value;
        }
    }

    /**
     * Set the router variable array
     *
     * @param array   $vars  An associative array with variables
     * @param boolean $merge If True, the array will be merged instead of overwritten
     *
     * @return void
     *
     * @since   11.1
     */
    public function setVars($vars = array(), $merge = true)
    {
        if ($merge) {
            $this->_vars = array_merge($this->_vars, $vars);
        } else {
            $this->_vars = $vars;
        }
    }

    /**
     * Get a router variable
     *
     * @param string $key The name of the variable
     *
     * @return mixed Value of the variable
     *
     * @since   11.1
     */
    public function getVar($key)
    {
        $result = null;
        if (isset($this->_vars[$key])) {
            $result = $this->_vars[$key];
        }

        return $result;
    }

    /**
     * Get the router variable array
     *
     * @return array An associative array of router variables
     *
     * @since   11.1
     */
    public function getVars()
    {
        return $this->_vars;
    }

    protected function _buildRawRoute(&$uri)
    {
        $vars	= array();
        // $app	= JApplication::getInstance('site');
        $app = JFactory::getApplication();
        // $menu	= $app->getMenu(true);
        $menu = array();

        //Handle an empty URL (special case)
        if (!$uri->getVar('Itemid') && !$uri->getVar('option')) {
            $item = $menu->getDefault(JFactory::getLanguage()->getTag());
            if (!is_object($item)) {
                // No default item set
                return $vars;
            }

            //Set the information in the request
            $vars = $item->query;

            //Get the itemid
            $vars['Itemid'] = $item->id;

            // Set the active menu item
            $menu->setActive($vars['Itemid']);

            return $vars;
        }

        //Get the variables from the uri
        $this->setVars($uri->getQuery(true));

        //Get the itemid, if it hasn't been set force it to null
        $this->setVar('Itemid', JRequest::getInt('Itemid', null));

        // Only an Itemid  OR if filter language plugin set? Get the full information from the itemid
        if (count($this->getVars()) == 1 || ( $app->getLanguageFilter() && count( $this->getVars()) == 2 )) {

            $item = $menu->getItem($this->getVar('Itemid'));
            if ($item !== NULL && is_array($item->query)) {
                $vars = $vars + $item->query;
            }
        }

        // Set the active menu item
        $menu->setActive($this->getVar('Itemid'));

        return $vars;
    }

    protected function _buildSefRoute(&$uri)
    {

        $vars	= array();
        $app	= JFactory::getApplication();
        // $menu	= $app->getMenu(true);
        $menu   = null;
        $route	= $uri->getPath();

        // Get the variables from the uri
        $vars = $uri->getQuery(true);

        $url = $this->CobaltBuildRoute($vars);
        $this->setVars($url,false);

        return $url;
    }

    protected function _processParseRules(&$uri)
    {
        $vars = array();

        return $vars;
    }

    protected function _processBuildRules(&$uri)
    {
        // Make sure any menu vars are used if no others are specified
        if (($this->_mode != JROUTER_MODE_SEF) && $uri->getVar('Itemid') && count($uri->getQuery(true)) == 2) {

            $app	= JFactory::getApplication();
            $menu	= $app->getMenu();

            // Get the active menu item
            $itemid = $uri->getVar('Itemid');
            $item = $menu->getItem($itemid);

            if ($item) {
                $uri->setQuery($item->query);
            }
            $uri->setVar('Itemid', $itemid);
        }

        // Process the attached build rules
        parent::_processBuildRules($uri);

        // Get the path data
        $route = $uri->getPath();

        if ($this->_mode == JROUTER_MODE_SEF && $route) {

            if ($limitstart = $uri->getVar('limitstart')) {
                $uri->setVar('start', (int) $limitstart);
                $uri->delVar('limitstart');
            }
        }

        $uri->setPath($route);
    }

    protected function _createURI($url)
    {
        //Create the URI
        $uri = parent::_createURI($url);

        // Set URI defaults
        $app	= JApplication::getInstance('site');
        $menu	= $app->getMenu();

        // Get the itemid form the URI
        $itemid = $uri->getVar('Itemid');

        if (is_null($itemid)) {
            if ($option = $uri->getVar('option')) {
                $item  = $menu->getItem($this->getVar('Itemid'));
                if (isset($item) && $item->component == $option) {
                    $uri->setVar('Itemid', $item->id);
                }
            } else {
                if ($option = $this->getVar('option')) {
                    $uri->setVar('option', $option);
                }

                if ($itemid = $this->getVar('Itemid')) {
                    $uri->setVar('Itemid', $itemid);
                }
            }
        } else {
            if (!$uri->getVar('option')) {
                if ($item = $menu->getItem($itemid)) {
                    $uri->setVar('option', $item->component);
                }
            }
        }

        return $uri;
    }

    public function CobaltBuildRoute(&$query)
    {
        $array = array();

        if ( array_key_exists('view',$query) ) {
            $array[] = $query['view'];
        }

        if ( array_key_exists('id',$query) ) {
            $array[] = $query['id'];
        }

        if ( array_key_exists('company_id',$query) ) {
            $array[] = $query['company_id'];
            unset($query['company_id']);
        }

        if ( array_key_exists('layout',$query)) {
            if ($query['layout'] == 'edit') {
                $array[] = $query['layout'];
            }
            if ($query['view'] == "reports") {
                $array[] = $query['layout'];
            }
            if ($query['view'] == "import") {
                $array[] = $query['layout'];
            }
            if ($query['view'] == "companies") {
                if ($query['layout'] != "edit") {
                    $array[] = $query['layout'];
                }
            }
            if ($query['view'] == "goals") {
                if ($query['layout'] != "edit") {
                    $array[] = $query['layout'];
                }
                if ( array_key_exists('type',$query) ) {
                    $array[] = $query['type'];
                    unset($query['type']);
                }
            }
            if ($query['layout'] == "edit_task") {
                $array[] = $query['layout'];
            }
        }

        if ( array_key_exists('import_type',$query) ) {
            $array[] = $query['import_type'];
            unset($query['import_type']);
        }

        unset($query['view']);
        unset($query['layout']);
        unset($query['id']);

        return $array;

    }

    public function CobaltParseRoute($segments)
    {
        $vars = array();
        $length = count($segments);

        if (isset($segments['view'])) {
            $vars['view'] = $segments['view'];
        }

        if (isset($segments[0]) && file_exists(JPATH_BASE.'/libraries/crm/view/'.$segments[0].'/html.php') ) {
            $vars['view'] = $segments[0];
        }

        if ( array_key_exists(1,$segments) ) {
            if ($segments[1] == "edit") {
                $vars['layout'] = "edit";
            } elseif ($segments[0] == "reports") {
                $vars['layout'] = $segments[1];

            } elseif ($segments[0] == "import") {
                if ($segments[1] == "review") {
                    $vars['layout'] = "review";
                } else {
                    $vars['layout'] = "import";
                    $vars['import_type'] = $segments[1];
                }
            } elseif ($segments[0] == "documents") {
                if ( array_key_exists(1,$segments) && $segments[1] == "download" ) {
                    $vars['layout'] = "download";
                    $vars['document'] = $segments[2];
                    unset($segments);
                }
            } else {
                $vars['id'] = $segments[1];
                switch ($vars['view']) {
                    case "deals":
                        $layout = "deal";
                    break;
                    case "people":
                        $layout = "person";
                    break;
                    case "companies":
                        $layout = "company";
                    break;
                    case "events":
                        $layout = "event";
                    break;
                }
                if ( isset($layout) ) {
                    $vars['layout'] = $layout;
                }
            }
        }


        if ( array_key_exists(2,$segments) ) {
            $vars['id'] = $segments[1];
            $vars['layout'] = $segments[2];
        }

        if ( array_key_exists("view",$vars) && $vars['view'] == "goals" ) {
            if ( array_key_exists(1,$segments) ) {
                if ( array_key_exists(2,$segments) ) {
                    $vars['layout'] = "edit";
                    $vars['type'] = $segments[2];
                } else {
                    $vars['layout'] = "add";
                }
            }
        }

        if ( array_key_exists("view",$vars) && $vars['view'] == "print" ) {
            $vars['tmpl'] = "component";
            if (array_key_exists(1,$segments)) {
                $vars['layout'] = $segments[1];
            }
            if ( array_key_exists(2,$segments) ) {
                $vars['item_id'] = $segments[2];
            }
        }

        if ( array_key_exists("view",$vars) && $vars['view'] == "events" ) {
            if ( array_key_exists(1,$segments) ) {
                if ($segments[1]=="edit_task") {
                    $vars['layout'] = "edit_task";
                }
            }
        }

        // if (array_key_exists('mobile',$_SESSION)) {
        //     if ($_SESSION['mobile'] == "yes") {
        //         $vars['mobile'] = "yes";
        //     }
        //     if ($_SESSION['mobile'] == "no") {
        //         $vars['mobile'] = "no";
        //     }
        // }
        return $vars;
    }
}
