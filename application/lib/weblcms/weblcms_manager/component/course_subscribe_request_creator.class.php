<?php
/**
 * $Id: request.class.php 224 2010-04-06 14:40:30Z Yannick $
 * @package applicatie.lib.weblcms.weblcms_manager.component
 */

require_once dirname(__FILE__) . '/../../course/course_request_form.class.php';

class WeblcmsManagerCourseSubscribeRequestCreatorComponent extends WeblcmsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {        
    	$course_code = Request :: get(WeblcmsManager :: PARAM_COURSE);
        $failures = 0;
                
        $trail = BreadcrumbTrail :: get_instance();
        /*
        if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header($trail);
            Display :: warning_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        } 
        */      
        $course = $this->retrieve_course($course_code);
        $request = new CourseRequest();
        $form = new CourseRequestForm(CourseRequestForm :: TYPE_CREATE, $this->get_url(array(WeblcmsManager :: PARAM_COURSE => $course_code)), $course, $this, $request, true);
       
        if($form->validate())
        {
			$success_request = $form->create_request();
        	$array_type = array();
	        $array_type['go'] = WeblcmsManager :: ACTION_VIEW_WEBLCMS_HOME;
            $this->redirect(Translation :: get($success_request ? 'RequestCreated' : 'RequestNotCreated'), ($success_request ? false : true), $array_type, array(WeblcmsManager :: PARAM_COURSE)); 	
        }
        else
        {
            $this->display_header();
            $form->display();
            $this->display_footer();
        }   
     }

     function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_home_url(), Translation :: get('WeblcmsManagerHomeComponent')));

        $breadcrumbtrail->add(new Breadcrumb($this->get_url(null, array(Application :: PARAM_ACTION, WeblcmsManager :: PARAM_COURSE)),Translation :: get('MyCourses')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => WeblcmsManager :: ACTION_SUBSCRIBE), array(WeblcmsManager :: PARAM_COURSE)),Translation :: get('CourseSubscribe')));
        $breadcrumbtrail->add_help('course request');


        $breadcrumbtrail->add_help('update request');
    }

    function get_additional_parameters()
    {
        return array();
    }
}
?>