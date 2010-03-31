<?php
/**
 * $Id: user_data_manager.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @author Sven Vanpoucke
 * @package user.lib
 */
//require_once Path :: get_application_path().'/lib/weblcms/data_manager/database.class.php';
/**
 * This is a skeleton for a data manager for the Users table.
 * Data managers must extend this class and implement its abstract methods.

 */
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
     * @return UserDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '.class.php';
            $class = $type . 'UserDataManager';
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
                    if ($authentication->can_register_new_user())
                    {
                        if ($authentication->register_new_user($username, $password))
                        {
                            $authentication_dir->close();
                            return $this->retrieve_user_by_username($username);
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
        // TODO: Check if the user can be deleted (fe: can an admin delete another admin etc)


        //A check to not delete a user when he's an active teacher
        //        {
        //            $courses = WebLcmsDataManager :: get_instance()->retrieve_courses(new EqualityCondition(Course :: PROPERTY_TITULAR,$user->get_id()))->size();
        //            if($courses>0)
        //            return false;
        //        }


        return true;
    }
}
?>