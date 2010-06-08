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
     * Constructor
     * @param WeblcmsBrowserComponent $browser
     */
    function AdminRequestBrowserTableCellRenderer($browser)
    {
        parent :: __construct($browser);
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
        	
        	case CommonRequest :: PROPERTY_MOTIVATION :
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
        
    	$check_item = $request->get_decision();
        if($check_item == CommonRequest :: NO_DECISION)
        {
        	$toolbar_data[] = array(
        		'href' => $this->browser->get_course_request_allowing_url($request, $this->browser->get_request_type(),$this->browser->get_request_view()),
        		'label' => Translation :: get('Allow'),
        		'img' => Theme :: get_common_image_path() . 'action_confirm.png');
        	
        	$toolbar_data[] = array(
	        	'href' => $this->browser->get_course_request_refuse_url($request, $this->browser->get_request_type(),$this->browser->get_request_view()),
	         	'label' => Translation :: get('Refuse'),
	         	'img' => Theme :: get_common_image_path() . 'action_refuse.png');
        }/*
        else
        {
        	$toolbar_data[] = array(
        		'label' => Translation :: get('Allow_NA'),
        		'img' => Theme :: get_common_image_path() . 'action_verify_na.png');
        }
		*/
        $toolbar_data[] = array(
        	'href' => $this->browser->get_course_request_deleting_url($request, $this->browser->get_request_type(),$this->browser->get_request_view()),
        	 'label' => Translation :: get('Delete'), 
        	 'img' => Theme :: get_common_image_path() . 'action_delete.png', 'confirm' => true);
              
        $toolbar_data[] = array(
        	'href' =>$this->browser->get_course_request_viewing_url($request, $this->browser->get_request_type(),$this->browser->get_request_view()),
        	'label' => Translation :: get('view'),
        	'img' => Theme :: get_common_image_path() . 'action_view.png');
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>