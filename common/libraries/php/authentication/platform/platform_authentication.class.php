<?php
namespace common\libraries;

use user\User;
/**
 * $Id: platform_authentication.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.authentication.platform
 */
require_once dirname(__FILE__) . '/../authentication.class.php';
/**
 * This authentication class implements the default authentication method for
 * the platform using hashed passwords.
 */
class PlatformAuthentication extends Authentication implements ChangeablePassword, ChangeableUsername
{

    function PlatformAuthentication()
    {
    }

    public function check_login($user, $username, $password = null)
    {
        $user_expiration_date = $user->get_expiration_date();
        $user_activation_date = $user->get_activation_date();

        if (($user_expiration_date != '0' && $user_expiration_date < time()) || ($user_activation_date != '0' && $user_activation_date > time()) || ! $user->get_active())
        {
            return Translation :: get("AccountNotActive");
        }
        else
        {
            if ($user->get_username() == $username && $user->get_password() == Hashing :: hash($password))
                return 'true';

            return Translation :: get("UsernameOrPasswordIncorrect");
        }
    }

    /**
     * We're changing a local password, so just set the user's new
     * password and it will be updated automatically when the form
     * is processed.
     *
     * @see Authentication :: change_password()
     */
    function change_password($user, $old_password, $new_password)
    {
        // Check whether we have an actual User object
        if (!$user instanceof User)
        {
            return false;
        }

        // Check whether the current password is different from the new password
        if ($old_password == $new_password)
        {
            return false;
        }

        $user->set_password(Hashing :: hash($new_password));
    	return true;
    }

    function get_password_requirements()
    {
        return Translation :: get('GeneralPasswordRequirements');
    }

    function is_configured()
    {
        return true;
    }
}
?>