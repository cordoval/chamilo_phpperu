<?php
/**
 * $Id: settings_weblcms_connector.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.reporting.settings
 */
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_data_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/course/course.class.php';
require_once Path :: get_library_path() . 'utilities.class.php';
require_once Path :: get_library_path() . 'filesystem/path.class.php';

/**
 * Simple connector class to facilitate rendering settings forms by
 * preprocessing data from the datamanagers to a simple array format.
 * @author Hans De Bisschop
 */

class SettingsWeblcmsConnector
{

    function get_course_layouts()
    {
        return CourseLayout :: get_layouts();
    }

    function get_tool_shortcut_options()
    {
        return CourseLayout :: get_tool_shortcut_options();
    }

    function get_course_menu_options()
    {
        return CourseLayout :: get_menu_options();
    }

    function get_breadcrumb_options()
    {
        return CourseLayout :: get_breadcrumb_options();
    }
    
    function get_languages()
    {
    	$adm = AdminDataManager :: get_instance();
		return $adm->get_languages();
    }
}
?>
