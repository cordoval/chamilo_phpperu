<?php
namespace common\libraries;

abstract class LauncherApplication extends Application
{
    const CLASS_NAME = __CLASS__;

    static function get_application_path($application_name)
    {
    	return Path :: get_common_libraries_class_path() . 'launcher/';
    }

    static function get_application_manager_path($application_name)
    {
        return self :: get_application_path($application_name) . $application_name . '/' . $application_name . '_launcher.class.php';
    }
    
    static function get_application_web_path($application_name)
    {
    	
    }

    static function get_application_class_name($application)
    {
        return Application :: application_to_class($application) . 'Launcher';
    }

    static function factory($application, $user = null)
    {
        require_once self :: get_application_manager_path($application);
        $class = __NAMESPACE__ . '\\' . self :: get_application_class_name($application);
        return new $class($user);
    }

    function display_header($breadcrumbtrail = null, $display_title = true)
    {
        Display :: small_header();
    }

    function display_footer()
    {
        Display :: small_footer();
    }
    
    static function exists($application)
    {
    	$launcher_file = self :: get_application_manager_path($application);
        
        return file_exists($launcher_file);
    }
}