<?php
/**
 * $Id: maintenance_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.maintenance
 */

require_once dirname(__FILE__) . '/inc/maintenance_wizard.class.php';
/**
 * This tool implements some maintenance tools for a course.
 * It gives a course administrator the possibilities to copy course content,
 * remove publications from the course, create & restore backups,...
 */
class MaintenanceTool extends Tool
{

    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('courses maintenance');
        
        if (! $this->get_course()->is_course_admin($this->get_parent()->get_user()))
        {
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        $wizard = new MaintenanceWizard($this);
        $wizard->run();
    }
    
	function get_application_component_path()
	{
		return dirname(__FILE__) . '/component/';
	}
}
?>