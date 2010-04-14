<?php
/**
 * $Id: admin_request_browser_table_cell_renderer.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component.admin_request_browser
 */
require_once dirname(__FILE__) . '/admin_request_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../course/course_request_table/default_course_request_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../course/course_request.class.php';
require_once dirname(__FILE__) . '/../../weblcms_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class AdminRequestBrowserTableCellRenderer extends DefaultCourseRequestTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param WeblcmsBrowserComponent $browser
     */
    function AdminRequestBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $request)
    {
        if ($column === AdminRequestBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($request);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
        	
        	case CourseRequest :: PROPERTY_MOTIVATION :
				$motivation = strip_tags(parent :: render_cell($column, $request));
				if(strlen($motivation) > 175)
				{
					$motivation = mb_substr($motivation,0,200).'&hellip;';
				}
				return $motivation;
				
        	//case Course::PROPERTY_COURSE_TYPE_ID: return WeblcmsDatamanager::get_instance()->retrieve_course_type($course->get_course_type_id())->get_name();
            // Exceptions that need post-processing go here ...
        }
        return parent :: render_cell($column, $request);
    }

    /**
     * Gets the action links to display
     * @param Course $course The course for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($request)
    {
        $toolbar_data = array();
        
        //$toolbar_data[] = array('href' => $this->browser->get_course_type_viewing_url($course_type), 'label' => Translation :: get('CourseTypeHome'), 'img' => Theme :: get_common_image_path() . 'action_home.png');
        //$toolbar_data[] = array('href' => $this->browser->get_course_request_editing_url($request), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
        
        //$toolbar_data[] = array('href' => $this->browser->get_course_viewing_url($course), 'label' => Translation :: get('CourseHome'), 'img' => Theme :: get_common_image_path() . 'action_home.png');
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>