<?php
/**
 * $Id: request.class.php 224 2010-04-06 14:40:30Z Yannick $
 * @package applicatie.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/../weblcms_manager.class.php';
require_once dirname(__FILE__) . '/../weblcms_manager_component.class.php';
require_once dirname(__FILE__) . '/../../course/course_request_form.class.php';

class WeblcmsManagerCourseCreateRequestComponent extends WeblcmsManagerComponent
{

	private $form;
    /**
     * Runs this component and displays its output.
     */
    function run()
    {        
    	$course_code = Request :: get(WeblcmsManager :: PARAM_COURSE);
        $failures = 0;
                
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(null, array(Application :: PARAM_ACTION)), Translation :: get('MyCourses')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CourseSubscribe')));
        $trail->add_help('course request');
        
        if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header($trail);
            Display :: warning_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }        
        //$course = $this->retrieve_course(new EqualityCondition(COURSE :: PROPERTY_ID, Request :: get(WeblcmsManager :: PARAM_COURSE)));
        $course = $this->get_course();
        $this->form = new CourseRequestForm(CourseRequestForm :: TYPE_CREATE,$this->get_url(array(WeblcmsManager :: PARAM_COURSE => $course_code)),$course,$this);
        /*
        if ($this->form->validate())
        {   	        	
        	if (! empty($course_codes))
        	{
            	if (! is_array($course_codes))
            	{
               	 	$course_codes = array($course_codes);
            	}
            
            	foreach ($course_codes as $course_code)
            	{
            	       
                	if (! $this->move_course($course_code))
                	{
                    	$failures ++;
                	}
            	}
            
            	if ($failures)
            	{
                	if (count($course_codes) == 1)
                	{
                    	$message = 'SelectedCourseMoved';
                	}
               	 	else
                	{
                	    $message = 'SelectedCoursesNotMoved';
                	}
          	  	}
          	  	else
           	 	{
             	   if (count($course_codes) == 1)
             	   {
             	       $message = 'SelectedCourseMoved';
             	   }
             	   else
              	   {
              	      $message = 'SelectedCoursesMoved';
              	   }
            	}
            	$parent = $this->form->get_new_parent();
            	$this->redirect(!$failures ? Translation :: get('CourseMoved') : Translation :: get('CourseNotMoved'), !$failures ? (false) : true, array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_BROWSER, WeblcmsManager :: PARAM_COURSE_TYPE => $parent));    	
            }
            else
            {
            	$this->display_error_page(htmlentities(Translation :: get('NoCourseSelected')));
            }	
        }
        else
        {
        */
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('RequestForm')));
            $this->display_header($trail);
            //echo Translation :: get('Course') . ': ' . $course->get_name();
            $this->form->display();
            $this->display_footer();
        //}
        
    	
    }
}
?>
