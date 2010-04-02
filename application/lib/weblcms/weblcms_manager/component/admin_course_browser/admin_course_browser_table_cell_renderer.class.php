<?php
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
        	case Course::PROPERTY_COURSE_TYPE_ID: return WeblcmsDatamanager::get_instance()->retrieve_course_type($course->get_course_type_id())->get_name();
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
        $toolbar_data = array();
        
        $toolbar_data[] = array('href' => $this->browser->get_course_viewing_url($course), 'label' => Translation :: get('CourseHome'), 'img' => Theme :: get_common_image_path() . 'action_home.png');
        
        $toolbar_data[] = array('href' => $this->browser->get_course_editing_url($course), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
        $toolbar_data[] = array('href' => $this->browser->get_course_deleting_url($course), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png');
        
        $toolbar_data[] = array('href' => $this->browser->get_course_maintenance_url($course), 'label' => Translation :: get('Maintenance'), 'img' => Theme :: get_common_image_path() . 'action_maintenance.png');
        
        $params = array();
        $params[WeblcmsManager :: PARAM_COURSE] = $course->get_id();
        $params[ReportingManager::PARAM_TEMPLATE_ID] = Reporting::get_name_registration(Utilities::camelcase_to_underscores('CourseStudentTrackerReportingTemplate'), WeblcmsManager::APPLICATION_NAME)->get_id();
        $url = ReportingManager :: get_reporting_template_registration_url_content($this->browser, $params);
        //$unsubscribe_url = $this->browser->get_url($parameters);
        $toolbar_data[] = array('href' => $url, 'label' => Translation :: get('Report'), 'img' => Theme :: get_common_image_path() . 'action_reporting.png');
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>