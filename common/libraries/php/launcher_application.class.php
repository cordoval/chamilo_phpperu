<?php
abstract class LauncherApplication extends Application
{
    const CLASS_NAME = __CLASS__;

    static function get_application_path($application_name)
    {
        return Path :: get_library_path() . 'launcher/';
    }

    static function get_application_manager_path($application_name)
    {
        return self :: get_application_path($application_name) . $application_name . '/' . $application_name . '_launcher.class.php';
    }

    static function get_application_class_name($application)
    {
        return Application :: application_to_class($application) . 'Launcher';
    }

    static function factory($application, $user = null)
    {
        require_once self :: get_application_manager_path($application);
        $class = self :: get_application_class_name($application);
        return new $class($user);
    }

    function display_header()
    {
        Display :: small_header();
    }

    function display_footer()
    {
        Display :: small_footer();
    }
    
    static function exists($application)
    {
    	$application_path = self :: get_application_path($name);       
        
        if (file_exists($application_path) && is_dir($application_path) )
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
?>