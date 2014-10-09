<?php

namespace Cobalt\Helper;

// no direct access
use Joomla\Http\HttpFactory;

defined('_CEXEC') or die('Restricted access');

class VersionHelper
{
    /**
     * Get the latest version from Cobalt.com
     *
     * @return string The latest Cobalt version
     */
    public static function getLatestVersion()
    {
	    // TODO - Make this work ;-)
	    return COBALT_VERSION;

	    $connector = HttpFactory::getHttp();

	    try
	    {
		    $data = $connector->get('http://www.cobaltcrm.org/remote/version.php');
	    }
	    catch (\DomainException $exception)
	    {
		    // TODO - Error handler
	    }

	    if ($data->code != 200)
	    {
		    // TODO - Error handler
	    }

	    // TODO - Process response object
    }
}
