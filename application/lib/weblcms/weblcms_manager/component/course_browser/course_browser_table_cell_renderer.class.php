<?php
/**
 * $Id: course_browser_table_cell_renderer.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component.course_browser
 */
require_once dirname(__FILE__) . '/course_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../course/course_table/default_course_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../course/course.class.php';
require_once dirname(__FILE__) . '/../../../course/course_rights.class.php';
require_once dirname(__FILE__) . '/../../weblcms_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class CourseBrowserTableCellRenderer extends DefaultCourseTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param WeblcmsBrowserComponent $browser
     */
    function CourseBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $course)
    {
        if ($column === CourseBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($course);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
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
    	
        if($this->browser->is_subscribed($course, $this->browser->get_user_id()))
        {
            return Translation :: get('AlreadySubscribed');
        }
        else
        {
        	$course = WeblcmsDataManager :: get_instance()->retrieve_course($course->get_id());
        	$current_right = $course->can_user_subscribe($this->browser->get_user());
        	
        	switch($current_right)
        	{
        		case CourseGroupSubscribeRight :: SUBSCRIBE_DIRECT :       			
        			$course_subscription_url = $this->browser->get_course_subscription_url($course);
        			$toolbar->add_item(new ToolbarItem(
			        		Translation :: get('Subscribe'),
			        		Theme :: get_common_image_path() . 'action_subscribe.png',
			        		$course_subscription_url,
			        		ToolbarItem :: DISPLAY_ICON
			        ));
        			break;
        		
        		case CourseGroupSubscribeRight :: SUBSCRIBE_REQUEST :
        			$conditions = array();
        			$date_conditions = array();
					
        			$conditions[] = new EqualityCondition(CourseRequest :: PROPERTY_COURSE_ID, $course->get_id());
        			$conditions[] = new EqualityCondition(CourseRequest :: PROPERTY_USER_ID, $this->browser->get_user_id());
        			$date_conditions[] = new InequalityCondition(CourseRequest :: PROPERTY_DECISION_DATE, InequalityCondition :: GREATER_THAN_OR_EQUAL, time());
        			$date_conditions[] = new EqualityCondition(CourseRequest :: PROPERTY_DECISION_DATE, NULL);
        
        			$conditions[] = new OrCondition($date_conditions);
        			$condition = new AndCondition($conditions);
        			
        			$teller = WeblcmsDataManager :: get_instance()->count_requests_by_course($condition);
        			if($teller == 0)
        			{
        				$course_request_form_url = $this->browser->get_course_request_form_url($course);
        				$toolbar->add_item(new ToolbarItem(
				        		Translation :: get('Request'),
				        		Theme :: get_common_image_path() . 'action_request.png',
				        		$course_request_form_url,
				        		ToolbarItem :: DISPLAY_ICON
				        ));
        			}
        			else
        			{	
        				$toolbar->add_item(new ToolbarItem(
				        		Translation :: get('Pending'),
				        		Theme :: get_common_image_path() . 'status_pending.png',
				        		null,
				        		ToolbarItem :: DISPLAY_ICON
				        ));    			
        			}
        			break;
        			
        		case CourseGroupSubscribeRight :: SUBSCRIBE_CODE :     		
        			$course_code_url = $this->browser->get_course_code_url($course);
        			$toolbar->add_item(new ToolbarItem(
        				Translation :: get('Code'),
		        		Theme :: get_common_image_path() . 'action_code.png',
		        		$course_code_url,
		        		ToolbarItem :: DISPLAY_ICON
		        ));
        			break;
        			     			
        		default : return Translation :: get('SubscribeNotAllowed');	
        	}      		
        }  
        return $toolbar->as_html();
    }
}
?>