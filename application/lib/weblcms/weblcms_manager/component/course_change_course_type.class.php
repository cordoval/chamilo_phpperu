<?php
/**
 * $Id: course_change_course_type.class.php 224 2010-04-06 14:40:30Z Yannick $
 * @package applicatie.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/../weblcms_manager.class.php';
require_once dirname(__FILE__) . '/../weblcms_manager_component.class.php';
require_once dirname(__FILE__) . '/../../course/course_change_course_type_form.class.php';

class WeblcmsManagerCourseChangeCourseTypeComponent extends WeblcmsManager
{

	private $form;
    /**
     * Runs this component and displays its output.
     */
    function run()
    {        
    	if ($this->get_user()->is_platform_admin())
        {
            Header :: set_section('admin');
        }
        
        $trail = new BreadcrumbTrail();
        
    	if ($this->get_user()->is_platform_admin())
        {
        	$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
       		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => WeblcmsManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Courses')));
			$trail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_BROWSER )), Translation :: get('CourseList')));
        }     
        else
        {
        	$trail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => null)), Translation :: get('Courses')));
        	$trail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => null)), Translation :: get('CourseList')));
        }
        
        $trail->add_help('change_course_coursetype');
        
   		if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header($trail, false, true);
            echo '<div class="clear"></div><br />';
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
    	$course_codes = Request :: get(WeblcmsManager :: PARAM_COURSE);
        $failures = 0;        
                    
        $course = $this->retrieve_courses(new EqualityCondition(COURSE :: PROPERTY_ID, Request :: get(WeblcmsManager :: PARAM_COURSE)))->next_result();
        
        $this->form = new CourseChangeCourseTypeForm($this->get_url(array(WeblcmsManager :: PARAM_COURSE => $course_codes)), $course, $this->get_user());
        
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
                    	$message = 'SelectedCourseCourseTypeChanged';
                	}
               	 	else
                	{
                	    $message = 'SelectedCoursesCourseTypeNotChanged';
                	}
          	  	}
          	  	else
           	 	{
             	   if (count($course_codes) == 1)
             	   {
             	       $message = 'SelectedCourseCourseTypeChanged';
             	   }
             	   else
              	   {
              	      $message = 'SelectedCoursesCourseTypeChanged';
              	   }
            	}           	
            	$parent = $this->form->get_new_parent();
            	$this->redirect(!$failures ? Translation :: get('CourseCourseTypeChanged') : Translation :: get('CourseCourseTypeNotChanged'), !$failures ? (false) : true, array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_BROWSER, WeblcmsManager :: PARAM_COURSE_TYPE => $parent));    	
            }
            else
            {
            	$this->display_error_page(htmlentities(Translation :: get('NoCourseSelected')));
            }	
        }
        else
        {
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('ChangeCourseType')));
            $this->display_header($trail);
            $this->form->display();
            $this->display_footer();
        }
    }
    
	function move_course($course_code)
    	{
        	$new_course_type = $this->form->get_selected_course_type();       	
        	$wdm = WeblcmsDataManager :: get_instance();
        	$course = $wdm->retrieve_course($course_code);
        	$course->set_course_type_id($new_course_type);
        	return $course->update($course);
    	}
}
?>