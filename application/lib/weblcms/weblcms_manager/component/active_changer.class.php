<?php
/**
 * $Id: deleter.class.php 211 2010-03-23 13:28:39Z Yannick $
 * @package user.lib.user_manager.component
 */

require_once dirname(__FILE__) . '/../weblcms_manager.class.php';
require_once dirname(__FILE__) . '/../weblcms_manager_component.class.php';

class WeblcmsManagerActiveChangerComponent extends WeblcmsManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $course_type_id = Request :: get(WeblcmsManager :: PARAM_COURSE_TYPE);
		$active = Request :: get(WeblcmsManager :: PARAM_ACTIVE);        
        
	    if (! $this->get_user()->is_platform_admin())
        {
            $trail = new BreadcrumbTrail();
            $trail->add_help('course_type_activation_changer');
            $this->display_header($trail);
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        if(!is_array($course_type_id))
        {
        	$course_type_id = array($course_type_id);
        }
        
        if (count($course_type_id) > 0)
        {
        	$failures = 0;
        	
			foreach($course_type_id as $id)
			{
	            $course_type = $course_type = $this->get_parent()->retrieve_course_type($id);
	            
	            $course_type->set_active($active);
	            
	            if ($course_type->update())
	            {
	                Events :: trigger_event('update', 'course_type', array('target_course_type_id' => $course_type->get_id(), 'action_course_type_id' => $this->get_course_type()->get_id()));
	            }
	            else
	            {
	            	$failures++;
	            }
			}
            
			if($active == 0)
				$message = $this->get_result($failures, count($course_type_id), 'CourseTypeNotDeactivated' , 'CourseTypesNotDeactivated', 'CourseTypeDeactivated', 'CourseTypesDeactivated');
			else
				$message = $this->get_result($failures, count($course_type_id), 'CourseTypeNotActivated' , 'CourseTypesNotActivated', 'CourseTypeNotActivated', 'CourseTypesNotActivated');
			
            $this->redirect($message, ($failures > 0), array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_TYPE_BROWSER));
        
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
}
?>