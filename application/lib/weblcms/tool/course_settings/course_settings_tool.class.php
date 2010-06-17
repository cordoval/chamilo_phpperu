<?php
/**
 * $Id: course_settings_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_settings
 */

/**
 * This tool allows a user to publish course_settingss in his or her course.
 */
class CourseSettingsTool extends Tool
{
    /**
     * Inherited.
     */
    function run()
    {
        $action = $this->get_action();
        
        switch ($action)
        {
            case self :: ACTION_UPDATE :
                $component = $this->create_component('Updater');
                break;
            default :
                $component = $this->create_component('Updater');
        }
        $component->run();
    }
    
	function get_application_component_path()
	{
		return dirname(__FILE__) . '/component/';
	}
}
?>