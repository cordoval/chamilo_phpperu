<?php
/**
 * $Id: path.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.filesystem
 */
// The root paths
define('WEB_PATH', 'WEB_PATH');
define('SYS_PATH', 'SYS_PATH');
define('REL_PATH', 'REL_PATH');

// Platform-level paths
define('WEB_LIB_PATH', 'WEB_LIB_PATH');
define('SYS_LIB_PATH', 'SYS_LIB_PATH');
define('WEB_PLUGIN_PATH', 'WEB_PLUGIN_PATH');
define('SYS_PLUGIN_PATH', 'SYS_PLUGIN_PATH');
define('WEB_FILE_PATH', 'WEB_FILE_PATH');
define('SYS_FILE_PATH', 'SYS_FILE_PATH');
define('REL_FILE_PATH', 'REL_FILE_PATH');
define('WEB_LAYOUT_PATH', 'WEB_LAYOUT_PATH');
define('SYS_LAYOUT_PATH', 'SYS_LAYOUT_PATH');
define('WEB_LANG_PATH', 'WEB_LANG_PATH');
define('SYS_LANG_PATH', 'SYS_LANG_PATH');

// Some paths for the LCMS-applications
define('SYS_APP_PATH', 'SYS_APP_PATH');
define('WEB_APP_PATH', 'WEB_APP_PATH');
define('SYS_APP_LIB_PATH', 'SYS_APP_LIB_PATH');
define('SYS_APP_ADMIN_PATH', 'SYS_APP_ADMIN_PATH');
define('SYS_APP_CLASS_GROUP_PATH', 'SYS_APP_CLASS_GROUP_PATH');
define('SYS_APP_HELP_PATH', 'SYS_APP_HELP_PATH');
define('SYS_APP_RIGHTS_PATH', 'SYS_APP_RIGHTS_PATH');
define('SYS_APP_INSTALL_PATH', 'SYS_APP_INSTALL_PATH');
define('SYS_APP_MIGRATION_PATH', 'SYS_APP_MIGRATION_PATH');
define('SYS_APP_REPOSITORY_PATH', 'SYS_APP_REPOSITORY_PATH');
define('SYS_APP_USER_PATH', 'SYS_APP_USER_PATH');
define('SYS_APP_MENU_PATH', 'SYS_APP_MENU_PATH');
define('SYS_APP_HOME_PATH', 'SYS_APP_HOME_PATH');
define('SYS_APP_TRACKING_PATH', 'SYS_APP_TRACKING_PATH');
define('SYS_APP_REPORTING_PATH', 'SYS_APP_REPORTING_PATH');
define('SYS_APP_WEBSERVICE_PATH', 'SYS_APP_WEBSERVICE_PATH');
define('SYS_LAUNCH_APP_PATH', 'SYS_LAUNCH_APP_PATH');
define('WEB_LAUNCH_APP_PATH', 'WEB_LAUNCH_APP_PATH');

// Files-paths
define('WEB_ARCHIVE_PATH', 'WEB_ARCHIVE_PATH');
define('SYS_ARCHIVE_PATH', 'SYS_ARCHIVE_PATH');
define('WEB_FCK_PATH', 'WEB_FCK_PATH');
define('SYS_FCK_PATH', 'SYS_FCK_PATH');
define('WEB_GARBAGE_PATH', 'WEB_GARBAGE_PATH');
define('SYS_GARBAGE_PATH', 'SYS_GARBAGE_PATH');
define('WEB_REPO_PATH', 'WEB_REPO_PATH');
define('SYS_REPO_PATH', 'SYS_REPO_PATH');
define('WEB_TEMP_PATH', 'WEB_TEMP_PATH');
define('SYS_TEMP_PATH', 'SYS_TEMP_PATH');
define('WEB_USER_PATH', 'WEB_USER_PATH');
define('SYS_USER_PATH', 'SYS_USER_PATH');
define('WEB_SCORM_PATH', 'WEB_SCORM_PATH');
define('SYS_SCORM_PATH', 'SYS_SCORM_PATH');
define('WEB_HOTPOTATOES_PATH', 'WEB_HOTPOTATOES_PATH');
define('SYS_HOTPOTATOES_PATH', 'SYS_HOTPOTATOES_PATH');
define('WEB_CACHE_PATH', 'WEB_CACHE_PATH');
define('SYS_CACHE_PATH', 'SYS_CACHE_PATH');

class Path
{
    private static $path = array();

    public static function get($path_type)
    {
    	if (isset(self :: $path[$path_type]))
    	{
    		return self :: $path[$path_type];
    	}

        switch ($path_type)
        {
            case WEB_PATH :
                return self :: $path[$path_type] = Configuration :: get_instance()->get_parameter('general', 'root_web');
            case SYS_PATH :
                return self :: $path[$path_type] = realpath(dirname(__FILE__) . '/../../') . '/';
            case REL_PATH :
                $url_append = Configuration :: get_instance()->get_parameter('general', 'url_append');
                return self :: $path[$path_type] = (substr($url_append, - 1) === '/' ? $url_append : $url_append . '/');

            // Platform-level paths
            case WEB_LIB_PATH :
                return self :: $path[$path_type] = self :: get(WEB_PATH) . 'common/';
            case SYS_LIB_PATH :
                return self :: $path[$path_type] = self :: get(SYS_PATH) . 'common/';
            case WEB_PLUGIN_PATH :
                return self :: $path[$path_type] = self :: get(WEB_PATH) . 'plugin/';
            case SYS_PLUGIN_PATH :
                return self :: $path[$path_type] = self :: get(SYS_PATH) . 'plugin/';
            case WEB_FILE_PATH :
                return self :: $path[$path_type] = self :: get(WEB_PATH) . 'files/';
            case SYS_FILE_PATH :
                return self :: $path[$path_type] = self :: get(SYS_PATH) . 'files/';
            case REL_FILE_PATH :
                return self :: $path[$path_type] = self :: get(REL_PATH) . 'files/';
            case WEB_LAYOUT_PATH :
                return self :: $path[$path_type] = self :: get(WEB_PATH) . 'layout/';
            case SYS_LAYOUT_PATH :
                return self :: $path[$path_type] = self :: get(SYS_PATH) . 'layout/';
            case WEB_LANG_PATH :
                return self :: $path[$path_type] = self :: get(WEB_PATH) . 'languages/';
            case SYS_LANG_PATH :
                return self :: $path[$path_type] = self :: get(SYS_PATH) . 'languages/';

            // Some paths for the LCMS applications
            case SYS_APP_PATH :
                return self :: $path[$path_type] = self :: get(SYS_PATH) . 'application/';
            case WEB_APP_PATH :
                return self :: $path[$path_type] = self :: get(WEB_PATH) . 'application/';
            case SYS_APP_ADMIN_PATH :
                return self :: $path[$path_type] = self :: get(SYS_PATH) . 'admin/';
            case SYS_APP_CLASS_GROUP_PATH :
                return self :: $path[$path_type] = self :: get(SYS_PATH) . 'group/';
            case SYS_APP_HELP_PATH :
                return self :: $path[$path_type] = self :: get(SYS_PATH) . 'help/';
            case SYS_APP_RIGHTS_PATH :
                return self :: $path[$path_type] = self :: get(SYS_PATH) . 'rights/';
            case SYS_APP_INSTALL_PATH :
                return self :: $path[$path_type] = self :: get(SYS_PATH) . 'install/';
            case SYS_APP_MIGRATION_PATH :
                return self :: $path[$path_type] = self :: get(SYS_PATH) . 'migration/';
            case SYS_APP_REPOSITORY_PATH :
                return self :: $path[$path_type] = self :: get(SYS_PATH) . 'repository/';
            case SYS_APP_USER_PATH :
                return self :: $path[$path_type] = self :: get(SYS_PATH) . 'user/';
            case SYS_APP_MENU_PATH :
                return self :: $path[$path_type] = self :: get(SYS_PATH) . 'menu/';
            case SYS_APP_HOME_PATH :
                return self :: $path[$path_type] = self :: get(SYS_PATH) . 'home/';
            case SYS_APP_TRACKING_PATH :
                return self :: $path[$path_type] = self :: get(SYS_PATH) . 'tracking/';
            case SYS_APP_REPORTING_PATH :
                return self :: $path[$path_type] = self :: get(SYS_PATH) . 'reporting/';
            case SYS_APP_WEBSERVICE_PATH :
                return self :: $path[$path_type] = self :: get(SYS_PATH) . 'webservice/';
			case SYS_LAUNCH_APP_PATH :
                return self :: $path[$path_type] = self :: get_library_path() . 'launcher/';
			case WEB_LAUNCH_APP_PATH :
				return self :: $path[$path_type] = self :: get(WEB_LIB_PATH) . 'launcher/';
                
            // Application-paths
            case SYS_APP_LIB_PATH :
                return self :: $path[$path_type] = self :: get(SYS_APP_PATH) . 'common/';

            // Files-paths
            case WEB_ARCHIVE_PATH :
                return self :: $path[$path_type] = self :: get(WEB_FILE_PATH) . 'archive/';
            case SYS_ARCHIVE_PATH :
                return self :: $path[$path_type] = self :: get(SYS_FILE_PATH) . 'archive/';
            case WEB_TEMP_PATH :
                return self :: $path[$path_type] = self :: get(WEB_FILE_PATH) . 'temp/';
            case SYS_TEMP_PATH :
                return self :: $path[$path_type] = self :: get(SYS_FILE_PATH) . 'temp/';
            case WEB_USER_PATH :
                return self :: $path[$path_type] = self :: get(WEB_FILE_PATH) . 'userpictures/';
            case SYS_USER_PATH :
                return self :: $path[$path_type] = self :: get(SYS_FILE_PATH) . 'userpictures/';
            case WEB_FCK_PATH :
                return self :: $path[$path_type] = self :: get(WEB_FILE_PATH) . 'fckeditor/';
            case SYS_FCK_PATH :
                return self :: $path[$path_type] = self :: get(SYS_FILE_PATH) . 'fckeditor/';
            case REL_FCK_PATH :
                return self :: $path[$path_type] = self :: get(REL_FILE_PATH) . 'fckeditor/';
            case WEB_REPO_PATH :
                return self :: $path[$path_type] = self :: get(WEB_FILE_PATH) . 'repository/';
            case SYS_REPO_PATH :
                return self :: $path[$path_type] = self :: get(SYS_FILE_PATH) . 'repository/';
            case REL_REPO_PATH :
                return self :: $path[$path_type] = self :: get(REL_FILE_PATH) . 'repository/';
            case SYS_SCORM_PATH :
                return self :: $path[$path_type] = self :: get(SYS_FILE_PATH) . 'scorm/';
            case WEB_SCORM_PATH :
                return self :: $path[$path_type] = self :: get(WEB_FILE_PATH) . 'scorm/';
            case SYS_HOTPOTATOES_PATH :
                return self :: $path[$path_type] = self :: get(SYS_FILE_PATH) . 'hotpotatoes/';
            case WEB_HOTPOTATOES_PATH :
                return self :: $path[$path_type] = self :: get(WEB_FILE_PATH) . 'hotpotatoes/';
            case SYS_CACHE_PATH :
                return self :: $path[$path_type] = self :: get(SYS_FILE_PATH) . 'cache/';
            case WEB_CACHE_PATH :
                return self :: $path[$path_type] = self :: get(WEB_FILE_PATH) . 'cache/';
            default :
                return;
        }
    }

    public static function get_library_path()
    {
        return self :: get(SYS_LIB_PATH);
    }

    public static function get_repository_path()
    {
        return self :: get(SYS_APP_REPOSITORY_PATH);
    }

    public static function get_user_path()
    {
        return self :: get(SYS_APP_USER_PATH);
    }

    public static function get_home_path()
    {
        return self :: get(SYS_APP_HOME_PATH);
    }

    public static function get_menu_path()
    {
        return self :: get(SYS_APP_MENU_PATH);
    }

    public static function get_group_path()
    {
        return self :: get(SYS_APP_CLASS_GROUP_PATH);
    }

    public static function get_help_path()
    {
        return self :: get(SYS_APP_HELP_PATH);
    }

    public static function get_rights_path()
    {
        return self :: get(SYS_APP_RIGHTS_PATH);
    }

    public static function get_migration_path()
    {
        return self :: get(SYS_APP_MIGRATION_PATH);
    }

    public static function get_admin_path()
    {
        return self :: get(SYS_APP_ADMIN_PATH);
    }

    public static function get_plugin_path()
    {
        return self :: get(SYS_PLUGIN_PATH);
    }

    public static function get_language_path()
    {
        return self :: get(SYS_LANG_PATH);
    }

    public static function get_application_library_path()
    {
        return self :: get(SYS_APP_LIB_PATH);
    }

    public static function get_tracking_path()
    {
        return self :: get(SYS_APP_TRACKING_PATH);
    }

    public static function get_application_path()
    {
        return self :: get(SYS_APP_PATH);
    }
    
    public static function get_launcher_application_path($web = false)
    {
    	if ($web)
    	{
    		return self :: get(WEB_LAUNCH_APP_PATH);
    	}
    	return self :: get(SYS_LAUNCH_APP_PATH);
    }
    
    

    public static function get_reporting_path()
    {
        return self :: get(SYS_APP_REPORTING_PATH);
    }

    public static function get_webservice_path()
    {
        return self :: get(SYS_APP_WEBSERVICE_PATH);
    }

    public static function get_common_path()
    {
        return self :: get(SYS_LIB_PATH);
    }

    public static function get_web_application_path($application_name)
    {
        return Path :: get_application_path() . 'lib/' . $application_name . '/';
    }

    public static function get_web_application_component_path($application_name)
    {
        return self :: get_web_application_path($application_name) . '/lib/' . $application_name . '_manager/component/';
    }

    public static function get_temp_path()
    {
        return self :: get(SYS_TEMP_PATH);
    }

    public static function get_cache_path()
    {
        return self :: get(SYS_CACHE_PATH);
    }
    
    public static function get_layout_path()
    {
    	return self :: get(SYS_LAYOUT_PATH);
    }
}
?>