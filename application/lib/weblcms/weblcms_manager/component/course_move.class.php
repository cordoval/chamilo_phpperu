<?php
/**
 * $Id: mover.class.php 224 2010-04-06 14:40:30Z Yannick $
 * @package applicatie.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/../weblcms_manager.class.php';
require_once dirname(__FILE__) . '/../weblcms_manager_component.class.php';
require_once dirname(__FILE__) . '/../../course/course_move_form.class.php';

class WeblcmsManagerCourseMoveComponent extends WeblcmsManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {        
    	$course_codes = Request :: get(WeblcmsManager :: PARAM_COURSE);
        $failures = 0;
                
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(WeblcmsManager :: APPLICATION_NAME, array(WeblcmsManager :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_TYPE_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('CourseType')));
        $trail->add(new Breadcrumb(Redirect :: get_link(WeblcmsManager :: APPLICATION_NAME, array(WeblcmsManager :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_TYPE_BROWSER, 'selected' => WeblcmsManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Course')));
        $trail->add_help('course_type general');
        
        if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header($trail);
            Display :: warning_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }        
        $course = $this->retrieve_courses(new EqualityCondition(COURSE :: PROPERTY_ID, Request :: get(WeblcmsManager :: PARAM_COURSE)))->next_result();
        //$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_VIEW_COURSE, WeblcmsManager :: PARAM_COURSE => Request :: get(WeblcmsManager :: PARAM_COURSE))), $course->get_name()));

        $form = new CourseMoveForm($this->get_url(),$course);
        dump(count($course_codes));
        if ($form->validate())
        {   	        	
        	if (! empty($course_codes))
        	{
            	if (! is_array($course_codes))
            	{
               	 	$course_codes = array($course_codes);
            	}
            
            	foreach ($course_codes as $course_code)
            	{
            		$success = $form->move_course();           
                	if (! $course->update())
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
            	$parent = $form->get_new_parent();
            	$this->redirect($success ? Translation :: get('CourseMoved') : Translation :: get('CourseNotMoved'), $success ? (false) : true, array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_BROWSER, WeblcmsManager :: PARAM_COURSE_TYPE => $parent));    	
            }
            else
            {
            	$this->display_error_page(htmlentities(Translation :: get('NoCourseSelected')));
            }	
        }
        else
        {
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Move')));
            $this->display_header($trail);
            //echo Translation :: get('Course') . ': ' . $course->get_name();
            $form->display();
            $this->display_footer();
        }
    }
}
?>
