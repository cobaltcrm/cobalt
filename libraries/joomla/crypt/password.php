<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Crypt
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Joomla Platform Password Hashing Interface
 *
 * @package     Joomla.Platform
 * @subpackage  Crypt
 * @since       12.2
 */
interface JCryptPassword
{
    const BLOWFISH = '$2y$';

    const JOOMLA = 'Joomla';

    const PBKDF = '$pbkdf$';

    const MD5 = '$1$';

    /**
     * Creates a password hash
     *
     * @param string $password The password to hash.
     * @param string $prefix   The prefix of the hashing function.
     *
     * @return string The hashed password.
     *
     * @since   12.2
     */
    public function create($password, $prefix = '$2a$');

    /**
     * Verifies a password hash
     *
     * @param string $password The password to verify.
     * @param string $hash     The password hash to check.
     *
     * @return boolean True if the password is valid, false otherwise.
     *
     * @since   12.2
     */
    public function verify($password, $hash);
}
