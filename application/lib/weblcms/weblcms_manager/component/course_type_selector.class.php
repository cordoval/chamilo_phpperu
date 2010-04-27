<?php
/**
 * $Id: course_type_selector.class.php 218 2010-03-26 14:21:26Z Yannick & Tristan $
 * @package application.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/../weblcms_manager.class.php';

require_once dirname(__FILE__) . '/../../course_type/course_type_select_form.class.php';

/**
 * Weblcms component allows to select a coursetype
 */
class WeblcmsManagerCourseTypeSelectorComponent extends WeblcmsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
    	
    	if(!WeblcmsDataManager :: get_instance()->count_course_types())
    		$this->simple_redirect(array('go' => WeblcmsManager :: ACTION_CREATE_COURSE));
    	
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
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Select')));
        $trail->add_help('course type select');
        
        if (! $this->get_user()->is_teacher() && ! $this->get_user()->is_platform_admin())
        {
            $this->display_header($trail, false, true);
            echo '<div class="clear"></div><br />';
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }

        $course_type_id = $this->get_course_type()->get_id();
        $form = new CourseTypeSelectForm($this->get_url());
        
        if ($form->validate() || $form->get_size()==1)
        {
            $this->simple_redirect(array('go' => WeblcmsManager :: ACTION_CREATE_COURSE, 'course_type' => $form->get_selected_id()));
            
        }
        else
        {
            $this->display_header($trail, false, true);
            echo '<div class="clear"></div><br />';
            $form->display();
            $this->display_footer();
        }
    }
}
?>