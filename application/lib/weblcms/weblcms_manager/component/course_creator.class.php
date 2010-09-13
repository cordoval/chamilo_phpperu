<?php
/**
 * $Id: course_creator.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/../weblcms_manager.class.php';

require_once dirname(__FILE__) . '/../../course/course_form.class.php';

/**
 * Weblcms component allows the use to create a course
 */
class WeblcmsManagerCourseCreatorComponent extends WeblcmsManager
{

    /**
     * Runs this component and displays its output.
     */
		
    function run()
    {
    	$course_codes = Request :: get(WeblcmsManager :: PARAM_COURSE);
    	
        if ($this->get_user()->is_platform_admin())
        {
            Header :: set_section('admin');
        }
        
        $trail = BreadcrumbTrail :: get_instance();    
        
        
        if (! $this->get_user()->is_teacher() && ! $this->get_user()->is_platform_admin())
        {
            $this->display_header();
            echo '<div class="clear"></div><br />';
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        $course = $this->get_course();     
        $id = $course->get_id();
        $parameters = array();
        $course_type_id = Request::get('course_type');
        
        if(!is_null($course_type_id))
        {
        	$parameters = array('course_type'=>$course_type_id);
        }
        
        $url = $this->get_url($parameters);
        

        
	    if(empty($id))
	    {
			$form = new CourseForm(CourseForm :: TYPE_CREATE, $course, $this->get_user(), $url, $this);	     	
	    }
	    else
	    {
		    $form = new CourseForm(CourseForm :: TYPE_EDIT, $course, $this->get_user(), $url, $this);
	    }
        
        if ($form->validate())
        {
            if ($form->get_form_type() == CourseForm :: TYPE_CREATE && WebLcmsDataManager :: get_instance()->retrieve_courses(new EqualityCondition(Course :: PROPERTY_VISUAL, $form->exportValue(Course :: PROPERTY_VISUAL)))->next_result())
            {
                $this->display_header($trail, false, true);
                $this->display_error_message(Translation :: get('CourseCodeAlreadyExists'));
                $form->display();
                $this->display_footer();
            }
            else
            {
            	$success = $form->save();
            	$array_filter = array(WeblcmsManager :: PARAM_COURSE);
	        	$array_type = array();
	        	$array_type['go'] = WeblcmsManager :: ACTION_VIEW_WEBLCMS_HOME;
	        	if(get_class($success) == 'Course')
	        	{
	        		$array_type['course'] = $course->get_id();
	        		$array_type['go'] = WeblcmsManager :: ACTION_COURSE_CREATE_REQUEST_CREATOR;
	        		$array_filter = null;
	        	}
                $this->redirect(Translation :: get($success ? 'CourseSaved' : 'CourseNotSaved'), ($success ? false : true), $array_type, $array_filter);
            }
        }
        else
        {
            $this->display_header();
            echo '<div class="clear"></div><br />';
            echo '<div id="form_container">';
            $form->display();
            echo '</div>';
            $this->display_footer();
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('courses create');
        $breadcrumbtrail->add(new Breadcrumb($this->get_home_url(), Translation :: get('WeblcmsManagerHomeComponent')));

        if ($this->get_user()->is_platform_admin())
        {
            $breadcrumbtrail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
            $breadcrumbtrail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => WeblcmsManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Courses')));
        }
        else
        {
        	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => null)), Translation :: get('Courses')));
        }
        
//        $course = $this->get_course();
//        $id = $course->get_id();
//
//        if(!$id)
//        {
//        	$breadcrumbtrail->add(new Breadcrumb($this->get_url(), Translation :: get('Create')));
//        }
//        else
//        {
//        	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_BROWSER), array(WeblcmsManager :: PARAM_COURSE, WeblcmsManager :: PARAM_TOOL)), Translation :: get('CourseList')));
//        	$breadcrumbtrail->add(new Breadcrumb($this->get_url(), Translation :: get('Update')));
//        }
    }

    function get_additional_parameters()
    {
    	return array();
    }
}
?>