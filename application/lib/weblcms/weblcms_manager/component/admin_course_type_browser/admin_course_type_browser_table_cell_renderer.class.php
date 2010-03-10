<?php
/**
 * $Id: admin_course_type_browser_table_cell_renderer.class.php 218 2009-11-13 14:21:26Z yannick $
 * @package application.lib.weblcms.weblcms_manager.component.admin_course_type_browser
 */
require_once dirname(__FILE__) . '/admin_course_type_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../course_type/course_type_table/default_course_type_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../course_type/course_type.class.php';
require_once dirname(__FILE__) . '/../../weblcms_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class AdminCourseTypeBrowserTableCellRenderer extends DefaultCourseTypeTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param WeblcmsBrowserComponent $browser
     */
    function AdminCourseTypeBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $course_type)
    {
        if ($column === AdminCourseTypeBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($course);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
        }
        return parent :: render_cell($column, $course_type);
    }

    /**
     * Gets the action links to display
     * @param CourseType $course_type The course_type for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($course_type)
    {
        $toolbar_data = array();
        
        $toolbar_data[] = array('href' => $this->browser->get_course_type_viewing_url($course_type), 'label' => Translation :: get('CourseTypeHome'), 'img' => Theme :: get_common_image_path() . 'action_home.png');
        
        $toolbar_data[] = array('href' => $this->browser->get_course_type_editing_url($course_type), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
        $toolbar_data[] = array('href' => $this->browser->get_course_type_deleting_url($course_type), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png');
        
        $toolbar_data[] = array('href' => $this->browser->get_course_type_maintenance_url($course_type), 'label' => Translation :: get('Maintenance'), 'img' => Theme :: get_common_image_path() . 'action_maintenance.png');
        
        
        //$params = array();
        //$params[ReportingManager :: PARAM_COURSE_TYPE_ID] = $course_type->get_id();
        //$url = ReportingManager :: get_reporting_template_registration_url_content($this->browser, 'CourseStudentTrackerReportingTemplate', $params);
        //$unsubscribe_url = $this->browser->get_url($parameters);
        //$toolbar_data[] = array('href' => $url, 'label' => Translation :: get('Report'), 'img' => Theme :: get_common_image_path() . 'action_reporting.png');
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>
