<?php
/**
 * $Id: platform_authentication.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.authentication.platform
 */
require_once dirname(__FILE__) . '/../authentication.class.php';
/**
 * This authentication class implements the default authentication method for
 * the platform using hashed passwords.
 */
class PlatformAuthentication extends Authentication
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

    public function is_password_changeable()
    {
        return true;
    }

    public function is_username_changeable()
    {
        return true;
    }

    function is_configured()
    {
        return true;
    }
}
?>