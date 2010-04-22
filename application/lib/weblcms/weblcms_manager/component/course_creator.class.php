<?php
/**
 * $Id: course_creator.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/../weblcms_manager.class.php';
require_once dirname(__FILE__) . '/../weblcms_manager_component.class.php';
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
        if ($this->get_user()->is_platform_admin())
        {
            Header :: set_section('admin');
        }
        
        $trail = new BreadcrumbTrail();
        if ($this->get_user()->is_platform_admin())
        {
            $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
            $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => WeblcmsManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Courses')));
        }
        else
        	$trail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => null)), Translation :: get('Courses')));
        
        $trail->add_help('courses create');
        
        if (! $this->get_user()->is_teacher() && ! $this->get_user()->is_platform_admin())
        {
            $this->display_header($trail, false, true);
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
        
        if(!$id)
        {
        	$trail->add(new Breadcrumb($this->get_url(), Translation :: get('Create')));
        }
        else
        {
        	$trail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_BROWSER)), Translation :: get('CourseList')));
        	$trail->add(new Breadcrumb($this->get_url(), Translation :: get('Update')));
        }
        
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
	        	$array_type = array();
	        	$array_type['go'] = WeblcmsManager :: ACTION_VIEW_WEBLCMS_HOME;
	        	/*if($success ||  $form->get_form_type() == CourseForm :: TYPE_EDIT)
	        		$array_type['course'] = $course->get_id();*/
                $this->redirect(Translation :: get($success ? 'CourseSaved' : 'CourseNotSaved'), ($success ? false : true), $array_type);
            }
        }
        else
        {
            $this->display_header($trail, false, true);
            echo '<div class="clear"></div><br />';
            echo '<div id="form_container">';
            $form->display();
            echo '</div>';
            $this->display_footer();
        }
    }
}
?>