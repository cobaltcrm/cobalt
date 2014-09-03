<?php
/**
 * @package    Cobalt.CRM
 *
 * @copyright  Copyright (C) 2012 Cobalt. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Cobalt;

defined('_CEXEC') or die;

use Cobalt\Container;
use Cobalt\Model\User;

class ModularAuthenticate
{
    /**
     * Login authentication function.
     *
     * Username and encoded password are passed the onUserLogin event which
     * is responsible for the user validation. A successful validation updates
     * the current session record with the user's details.
     *
     * Username and encoded password are sent as credentials (along with other
     * possibilities) to each observer (authentication plugin) for user
     * validation.  Successful validation will update the current session with
     * the user details.
     *
     * @param array $credentials Array('username' => string, 'password' => string)
     * @param array $options     Array('remember' => boolean)
     *
     * @return boolean True on success.
     *
     * @since   11.1
     */
    public function login($credentials, $options = array())
    {
        $app = Container::get('app');

        // Get the global JAuthentication object.
        jimport('joomla.user.authentication');

        $authenticate = new JAuthentication;
        $response = $authenticate->authenticate($credentials, $options);

        if ($response->status === JAuthentication::STATUS_SUCCESS) {

            // validate that the user should be able to login (different to being authenticated)
            // this permits authentication plugins blocking the user
            $authorisations = $authenticate->authorise($response, $options);

            foreach ($authorisations as $authorisation) {
                $denied_states = array(JAuthentication::STATUS_EXPIRED, JAuthentication::STATUS_DENIED);
                if (in_array($authorisation->status, $denied_states)) {
                    // Trigger onUserAuthorisationFailure Event.
                    $app->triggerEvent('onUserAuthorisationFailure', array((array) $authorisation));

                    // If silent is set, just return false.
                    if (isset($options['silent']) && $options['silent']) {
                        return false;
                    }

                    // Return the error.
                    switch ($authorisation->status) {
                        case JAuthentication::STATUS_EXPIRED:
                            return JError::raiseWarning('102002', JText::_('JLIB_LOGIN_EXPIRED'));
                            break;
                        case JAuthentication::STATUS_DENIED:
                            return JError::raiseWarning('102003', JText::_('JLIB_LOGIN_DENIED'));
                            break;
                        default:
                            return JError::raiseWarning('102004', JText::_('JLIB_LOGIN_AUTHORISATION'));
                            break;
                    }
                }
            }

            // Import the user plugin group.
            JPluginHelper::importPlugin('user');

            // OK, the credentials are authenticated and user is authorised.  Lets fire the onLogin event.
            $results = $app->triggerEvent('onUserLogin', array((array) $response, $options));

            /*
             * If any of the user plugins did not successfully complete the login routine
             * then the whole method fails.
             *
             * Any errors raised should be done in the plugin as this provides the ability
             * to provide much more information about why the routine may have failed.
             */

            if ($response->status == 1) {

                $app->setUser(new JUser($response->user_id));

                return true;
            }
        }

        // Trigger onUserLoginFailure Event.
        $app->triggerEvent('onUserLoginFailure', array((array) $response));

        // If silent is set, just return false.
        if (isset($options['silent']) && $options['silent']) {
            return false;
        }

        // If status is success, any error will have been raised by the user plugin
        if ($response->status !== JAuthentication::STATUS_SUCCESS) {
            echo $response->error_message;
        }

        return false;
    }

    /**
     * Logout authentication function.
     *
     * Passed the current user information to the onUserLogout event and reverts the current
     * session record back to 'anonymous' parameters.
     * If any of the authentication plugins did not successfully complete
     * the logout routine then the whole method fails. Any errors raised
     * should be done in the plugin as this provides the ability to give
     * much more information about why the routine may have failed.
     *
     * @param integer $userid  The user to load - Can be an integer or string - If string, it is converted to ID automatically
     * @param array   $options Array('clientid' => array of client id's)
     *
     * @return boolean True on success
     *
     * @since   11.1
     */
    public function logout($userid = null, $options = array())
    {
        $app = JFactory::getApplication();

        // Get a user object from the JApplication.
        $user = JFactory::getUser($userid);

        // Build the credentials array.
        $parameters['username'] = $user->get('username');
        $parameters['id'] = $user->get('id');

        // Set clientid in the options array if it hasn't been set already.
        if (!isset($options['clientid'])) {
            $options['clientid'] = $app->getClientId();
        }

        // Import the user plugin group.
        JPluginHelper::importPlugin('user');

        // OK, the credentials are built. Lets fire the onLogout event.
        $event = $app->triggerEvent('onUserLogout', array($parameters, $options));

        // Check if any of the plugins failed. If none did, success.

        if ($event->isStopped() === false) {
            // Use domain and path set in config for cookie if it exists.
            $cookie_domain = $app->get('cookie_domain', '');
            $cookie_path = $app->get('cookie_path', '/');
            setcookie(self::getHash('JLOGIN_REMEMBER'), false, time() - 86400, $cookie_path, $cookie_domain);

            $app->setUser(null);

            return true;
        }

        // Trigger onUserLoginFailure Event.
        $app->triggerEvent('onUserLogoutFailure', array($parameters));

        return false;
    }

    public static function getHash($seed)
    {
        return md5(JFactory::getConfig()->get('secret') . $seed);
    }

}
