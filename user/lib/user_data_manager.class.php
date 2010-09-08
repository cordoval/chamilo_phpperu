<?php
/**
 * $Id: user_data_manager.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @author Hans De Bisschop
 * @author Sven Vanpoucke
 * @package user.lib
 *
 * This is a skeleton for a data manager for the User application.
 */

require_once Path :: get_application_path() . 'lib/weblcms/weblcms_data_manager.class.php';

class UserDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return UserDataManagerInterface The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '_user_data_manager.class.php';
            $class = Utilities :: underscores_to_camelcase($type) . 'UserDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    /**
     * Logs a user in to the platform
     * @param string $username
     * @param string $password
     */
    public function login($username, $password = null)
    {
        // If username is available, try to login
        if (! self :: get_instance()->is_username_available($username))
        {
            $user = self :: get_instance()->retrieve_user_by_username($username);
            $authentication_method = $user->get_auth_source();
            $authentication = Authentication :: factory($authentication_method);
            $message = $authentication->check_login($user, $username, $password);
            if ($message == 'true')
            {
                return $user;
            }
            return $message;
        }
        // If username is not available, check if an authentication method is able to register
        // the user in the platform
        else
        {
            $authentication_dir = dir(Path :: get_library_path() . 'authentication/');
            while (false !== ($authentication_method = $authentication_dir->read()))
            {
                if (strpos($authentication_method, '.') === false && is_dir($authentication_dir->path . '/' . $authentication_method) && PlatformSetting :: get('enable_' . $authentication_method))
                {
                    $authentication_class_file = $authentication_dir->path . '/' . $authentication_method . '/' . $authentication_method . '_authentication.class.php';
                    $authentication_class = ucfirst($authentication_method) . 'Authentication';
                    require_once $authentication_class_file;
                    $authentication = new $authentication_class();
                    if ($authentication instanceof UserRegistrationSupport)
                    {
                        if ($authentication->register_new_user($username, $password))
                        {
                            $authentication_dir->close();
                            return self :: get_instance()->retrieve_user_by_username($username);
                        }
                    }
                }
            }
            $authentication_dir->close();
            return Translation :: get('UsernameNotAvailable');
        }
    }

    /**
     * Logs the user out of the system
     */
    public function logout()
    {
        $user = self :: get_instance()->retrieve_user(Session :: get_user_id());
        $authentication_method = $user->get_auth_source();
        $authentication_class_file = Path :: get_library_path() . 'authentication/' . $authentication_method . '/' . $authentication_method . '_authentication.class.php';
        $authentication_class = ucfirst($authentication_method) . 'Authentication';
        require_once $authentication_class_file;
        $authentication = new $authentication_class();
        if ($authentication->logout($user))
        {
            return true;
        }
        return false;
    }

    /**
     * Checks whether the user is allowed to be deleted
     * Unfinished.
     */
    function user_deletion_allowed($user)
    {
        //A check to not delete a user when he's an active teacher
        $courses = WeblcmsDataManager :: get_instance()->count_courses(new EqualityCondition(Course :: PROPERTY_TITULAR,$user->get_id()));
        if($courses > 0)
        {
        	return false;
        }

        return true;
    }
}
?>