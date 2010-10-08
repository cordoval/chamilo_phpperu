<?php
require_once dirname(__FILE__) . '/cas/cas_authentication.class.php';
/**
 * $Id: authentication.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.authentication
 */
/**
 * An abstract class for handling authentication. Impement new authentication
 * methods by creating a class which extends this abstract class.
 */
abstract class Authentication
{

    /**
     * Checks if the given username and password are valid
     * @param string $username
     * @param string $password
     * @return true
     */
    abstract function check_login($user, $username, $password = null);

    /**
     * Changes the user's password
     * @param User The current user object
     * @param string The user's current password
     * @param string The desired new password
     * @return boolean True if changed, false if not
     */
    abstract function change_password($user, $old_password, $new_password);

    /**
     * Get any and every kind of password requirements for the
     * authentication method
     *
     * @return string Instructions for the password
     */
    abstract function get_password_requirements();

    /**
     * Registers a new user
     * @param string $username
     * @param string $password
     * @return boolean True on success, false if not
     */
    public function register_new_user($username, $password = null)
    {
        return false;
    }

    /**
     * Logs the current user out of the platform. The different authentication
     * methods can overwrite this function if additional operations are needed
     * before a user can be logged out.
     * @param User $user The user which is logging out
     */
    function logout($user)
    {
        Session :: destroy();
    }

    function is_valid()
    {
        $is_registration = Request :: get(Application :: PARAM_APPLICATION) == UserManager :: APPLICATION_NAME && Request :: get(Application :: PARAM_ACTION) == UserManager :: ACTION_REGISTER_USER;
        $is_invitation = Request :: get(Application :: PARAM_APPLICATION) == UserManager :: APPLICATION_NAME && Request :: get(Application :: PARAM_ACTION) == UserManager :: ACTION_REGISTER_INVITED_USER;
        $is_password_reset = Request :: get(Application :: PARAM_APPLICATION) == UserManager :: APPLICATION_NAME && Request :: get(Application :: PARAM_ACTION) == UserManager :: ACTION_RESET_PASSWORD;
        $is_online_page = Request :: get(Application :: PARAM_APPLICATION) == AdminManager :: APPLICATION_NAME && Request :: get(Application :: PARAM_ACTION) == AdminManager :: ACTION_WHOIS_ONLINE;
        $is_download_page = Request :: get(Application :: PARAM_APPLICATION) == RepositoryManager :: APPLICATION_NAME && Request :: get(Application :: PARAM_ACTION) == RepositoryManager :: ACTION_DOWNLOAD_DOCUMENT;

        $is_authentication_exception = $is_registration || $is_invitation || $is_password_reset || $is_online_page || $is_download_page;

        if ($is_authentication_exception)
        {
            return true;
        }

        // TODO: Add system here to allow authentication via encrypted user key ?
        if (! Session :: get_user_id())
        {
            // Check whether external authentication is enabled
            $allow_external_authentication = PlatformSetting :: get('enable_external_authentication');
            $no_external_authentication = Request :: get('noExtAuth');

            if ($allow_external_authentication && ! isset($no_external_authentication))
            {
                $external_authentication_types = self :: get_external_authentication_types();

                foreach ($external_authentication_types as $type)
                {
                    $allow_authentication = PlatformSetting :: get('enable_' . $type . '_authentication');
                    $no_authentication = Request :: get('no' . Utilities :: underscores_to_camelcase($type) . 'Auth');

                    if ($allow_authentication)
                    {
                        $authentication = self :: factory($type);
                        if ($authentication->check_login())
                        {
                            return true;
                        }
                    }
                }

                return false;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return true;
        }
    }

    /**
     * Creates an instance of an authentication class
     * @param string $authentication_method
     * @return Authentication An object of a class implementing this abstract
     * class.
     */
    function factory($authentication_method)
    {
        $authentication_class_file = dirname(__FILE__) . '/' . $authentication_method . '/' . $authentication_method . '_authentication.class.php';
        $authentication_class = Utilities :: underscores_to_camelcase($authentication_method) . 'Authentication';
        require_once $authentication_class_file;
        return new $authentication_class();
    }

    static function get_external_authentication_types()
    {
        $types = array();
        $types[] = 'invitation';
        $types[] = 'cas';
        return $types;
    }

    static function get_internal_authentication_types()
    {
        $types = array();
        $types[] = 'ldap';
        $types[] = 'platform';
        return $types;
    }
    
    static function is_valid_authentication_type($type)
    {
    	$types = array_merge(self :: get_external_authentication_types(), self :: get_internal_authentication_types());
    	return in_array($type, $types);
    }

    function get_configuration()
    {
        return array();
    }

    function is_configured()
    {
        $settings = $this->get_configuration();

        foreach ($settings as $setting => $value)
        {
            if (empty($value) || ! isset($value))
            {
                return false;
            }
        }

        return true;
    }
}
?>