<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

class CobaltHelperVersion
{
	public static function isUpToDate($current, $latest)
	{
		// Modify the revision so we can split it into an array easily
		$current	= str_replace('r', '.', $current);
		$latest	= str_replace('r', '.', $latest);

		$currentParts	= explode('.', $current);
		$latestParts	= explode('.', $latest);

		// Compare major version
		if (isset($currentParts['0']) && isset($latestParts['0']) && $currentParts['0'] < $latestParts['0']) {
			return false;
		}

		// Compare minor version
		if (isset($currentParts['1']) && isset($latestParts['1'])) {
			$difference = strlen($currentParts['1']) - strlen($latestParts['1']);

			if ($difference == 0) {
				if ($currentParts['1'] < $latestParts['1']) {
					return false;
				}
			} elseif ($difference < 0) {
				$n = ($difference * -1);
				for ($i=0; $i<$n; $i++) {
					$currentParts['1'] .= "0";
				}

				if ($currentParts['1'] < $latestParts['1']) {
					return false;
				}
			} else {
				for ($i=0, $n=$difference; $i<$n; $i++) {
					$latestParts['1'] .= "0";
				}

				if ($currentParts['1'] < $latestParts['1']) {
					return false;
				}
			}
		}

		// Compare revision
		if (isset($currentParts['2']) && isset($latestParts['2']) && $currentParts['2'] < $latestParts['2']) {
			return false;
		}

		return true;
	}

	/**
	 * Get the latest version from Cobalt.com
	 *
	 * @return string		The latest Cobalt version
	 */
	public static function getLatestVersion()
	{
		$session =& JFactory::getSession();
		// $latestVersion = $session->get('cobalt_version', null);
		$latestVersion = null;

		if (is_null($latestVersion)) {
			// Check if cURL is installed
			if ( function_exists('curl_init') ) {
				$latestVersion = self::get_url_contents("http://www.cobaltcrm.org/remote/version.php");
			} else {
				$latestVersion = 'no_curl';
			}

			$session->set('cobalt_version', $latestVersion);
		}

		return $latestVersion;
	}

	/**
	 * Get the response from the URL
	 *
	 * @param  string	$url	The URL to query
	 * @param  array	$post	The data to send to the remote site
	 * @return mixed			The result of curl_exec() if successful; False otherwise
	 */
	public function get_url_contents($url, $post=null)
	{
		$timeout = 5;
        $crl = curl_init($url);

        if (! is_null($post)) {
	        curl_setopt($crl, CURLOPT_POST, 1);
	        curl_setopt($crl, CURLOPT_POSTFIELDS, $post);
        }

        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);

        $ret = curl_exec($crl);

        curl_close($crl);

        if(strlen($ret) == 0) {
        	return false;
        } else {
        	return $ret;
        }
    }
}