<?php
/**
 * $Id: admin_course_type_browser_table_cell_renderer.class.php 218 2010-03-10 14:21:26Z yannick $
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
            return $this->get_modification_links($course_type);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            case CourseType :: PROPERTY_NAME :
				$name = parent :: render_cell($column, $course_type);
				$name_short = $name;
				if(strlen($name_short) > 53)
				{
					$name_short = mb_substr($name_short,0,50).'&hellip;';
				}
				return '<a href="'.htmlentities($this->browser->get_course_type_viewing_url($course_type)).'" title="'.$name.'">'.$name_short.'</a>';
        	
            case CourseType :: PROPERTY_DESCRIPTION :
				$description = strip_tags(parent :: render_cell($column, $course_type));
				if(strlen($description) > 175)
				{
					$description = mb_substr($description,0,170).'&hellip;';
				}
				return $description;
			/*
            case CourseType :: PROPERTY_ACTIVE :
            	$active = parent :: render_cell($column, $course_type);
        		$active_short = $active;
				if(strlen($active_short) > 53)
				{
					$active_short = mb_substr($active_short,0,50).'&hellip;';
				}
            	return '<a href"'.htmlentities($this->browser->get_course_type_changing_url($course_type)).'" title="'.$active.'">'.$active_short.'</a>';
            */
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
        //$toolbar_data[] = array('href' => $this->browser->get_course_type_viewing_url($course_type), 'label' => Translation :: get('CourseTypeHome'), 'img' => Theme :: get_common_image_path() . 'action_home.png');
        $toolbar_data[] = array('href' => $this->browser->get_course_type_editing_url($course_type), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
        $toolbar_data[] = array('href' => $this->browser->get_course_type_deleting_url($course_type), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png', 'confirm' => true);
        //$toolbar_data[] = array('href' => $this->browser->get_course_type_maintenance_url($course_type), 'label' => Translation :: get('Maintenance'), 'img' => Theme :: get_common_image_path() . 'action_maintenance.png');
        $toolbar_data[] = array('href' => $this->browser->get_change_active_url('course_type', $course_type->get_id()), 'label' => ($course_type->get_active() == 1) ? Translation :: get('Deactivate') : Translation :: get('Activate'), 'confirm' => false, 'img' => ($course_type->get_active() == 1) ? Theme :: get_common_image_path() . 'action_visible.png' : Theme :: get_common_image_path() . 'action_invisible.png');
        
        //$params = array();
        //$params[ReportingManager :: PARAM_COURSE_TYPE_ID] = $course_type->get_id();
        //$url = ReportingManager :: get_reporting_template_registration_url_content($this->browser, 'CourseStudentTrackerReportingTemplate', $params);
        //$unsubscribe_url = $this->browser->get_url($parameters);
        //$toolbar_data[] = array('href' => $url, 'label' => Translation :: get('Report'), 'img' => Theme :: get_common_image_path() . 'action_reporting.png');
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>