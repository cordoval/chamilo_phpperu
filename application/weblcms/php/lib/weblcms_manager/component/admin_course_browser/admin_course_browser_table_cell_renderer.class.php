<?php
namespace application\weblcms;

/**
 * $Id: admin_course_browser_table_cell_renderer.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component.admin_course_browser
 */
require_once dirname(__FILE__) . '/admin_course_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../course/course_table/default_course_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../course/course.class.php';
require_once dirname(__FILE__) . '/../../weblcms_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class AdminCourseBrowserTableCellRenderer extends DefaultCourseTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param WeblcmsBrowserComponent $browser
     */
    function AdminCourseBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $course)
    {
        if ($column === AdminCourseBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($course);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
        	
        	case Course::PROPERTY_COURSE_TYPE_ID: 
        		
        		if($course->get_course_type_id() != 0)
        			return WeblcmsDatamanager::get_instance()->retrieve_course_type($course->get_course_type_id())->get_name();
        		else
        		{
        			return Translation :: get('NoCourseType');
        		}
            // Exceptions that need post-processing go here ...
        }
        return parent :: render_cell($column, $course);
    }

    /**
     * Gets the action links to display
     * @param Course $course The course for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($course)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('CourseHome'),
        		Theme :: get_common_image_path() . 'action_home.png',
        		$this->browser->get_course_viewing_url($course),
        		ToolbarItem :: DISPLAY_ICON
        ));
        
        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Edit'),
        		Theme :: get_common_image_path() . 'action_edit.png',
        		$this->browser->get_course_editing_url($course),
        		ToolbarItem :: DISPLAY_ICON
        ));
        
        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Delete'),
        		Theme :: get_common_image_path() . 'action_delete.png',
        		$this->browser->get_course_deleting_url($course),
        		ToolbarItem :: DISPLAY_ICON,
        		true
        ));
        
        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('ChangeCourseType'),
        		Theme :: get_common_image_path() . 'action_move.png',
        		$this->browser->get_course_changing_course_type_url($course),
        		ToolbarItem :: DISPLAY_ICON
        ));
        
        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Maintenance'),
        		Theme :: get_common_image_path() . 'action_maintenance.png',
        		$this->browser->get_course_maintenance_url($course),
        		ToolbarItem :: DISPLAY_ICON
        ));
        
        $params = array();
        $params[WeblcmsManager :: PARAM_COURSE] = $course->get_id();
        //$params[ReportingManager::PARAM_TEMPLATE_ID] = Reporting::get_name_registration('course_student_tracker_reporting_template', WeblcmsManager::APPLICATION_NAME)->get_id();
        $url = $this->browser->get_reporting_url($params);
        
        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Report'),
        		Theme :: get_common_image_path() . 'action_reporting.png',
        		$url,
        		ToolbarItem :: DISPLAY_ICON
        ));
        
        return $toolbar->as_html();
    }
}
?>