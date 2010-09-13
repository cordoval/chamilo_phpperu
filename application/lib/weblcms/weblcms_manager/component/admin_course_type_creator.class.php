<?php
/**
 * $Id: admin_course_type_creator.class.php 1 2010-02-25 11:44:26Z Tristan $
 * @package application.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/../weblcms_manager.class.php';

require_once dirname(__FILE__) . '/../../course_type/course_type_form.class.php';

/**
 * Weblcms component allows the use to create a course_type
 */
class WeblcmsManagerAdminCourseTypeCreatorComponent extends WeblcmsManager
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

        $trail = BreadcrumbTrail :: get_instance();
        
        if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header();
            echo '<div class="clear"></div><br />';
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        $coursetype = $this->get_course_type();
        $parameter =array();
        $course_type_id = Request :: get("course_type");
        if(!is_null($course_type_id))
        	$parameter['course_type']=$course_type_id;

        if(is_null($course_type_id))
        {
	        $form = new CourseTypeForm(CourseTypeForm :: TYPE_CREATE, $coursetype, $this->get_url($parameter), $this);
        }	        
        else
        {
	        $form = new CourseTypeForm(CourseTypeForm :: TYPE_EDIT, $coursetype, $this->get_url($parameter), $this);
        }
	        
        if ($form->validate())
        {
	        $success = $form->save();
	        $array_type = array();
	        $array_type['go'] = WeblcmsManager :: ACTION_ADMIN_COURSE_TYPE_BROWSER;
            $this->redirect(Translation :: get($success ? 'CourseTypeSaved' : 'CourseTypeNotSaved'), ($success ? false : true), $array_type,array(),false,Redirect::TYPE_LINK );
        }
        else
        {
            $this->display_header();
            echo '<div class="clear">&nbsp;</div>';
            $form->display();
            $this->display_footer();
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_home_url(), Translation :: get('WeblcmsManagerHomeComponent')));

        if ($this->get_user()->is_platform_admin())
        {
            $breadcrumbtrail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
            $breadcrumbtrail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => WeblcmsManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Courses')));
            $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_TYPE_BROWSER)), Translation :: get('CourseTypeList')));

        }
        else
        {
            $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => null)), Translation :: get('Courses')));
        }
        $breadcrumbtrail->add_help('coursetypes create');
    }

    function get_additional_parameters()
    {
    	return array();
    }
}
?>