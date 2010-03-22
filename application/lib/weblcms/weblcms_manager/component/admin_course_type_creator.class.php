<?php
/**
 * $Id: admin_course_type_creator.class.php 1 2010-02-25 11:44:26Z Tristan $
 * @package application.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/../weblcms_manager.class.php';
require_once dirname(__FILE__) . '/../weblcms_manager_component.class.php';
require_once dirname(__FILE__) . '/../../course_type/course_type_form.class.php';

/**
 * Weblcms component allows the use to create a course_type
 */
class WeblcmsManagerAdminCourseTypeCreatorComponent extends WeblcmsManagerComponent
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
            $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => WeblcmsManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('CourseType')));
        }
        else
        	$trail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => null)), Translation :: get('CourseTypes')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateType')));
        $trail->add_help('coursetypes create');

        if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header($trail, false, true);
            echo '<div class="clear"></div><br />';
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        $coursetype = $this->get_course_type();
        $id = $coursetype->get_id();

        if(empty($id))
	        $form = new CourseTypeForm(CourseTypeForm :: TYPE_CREATE, $coursetype, $this->get_url(), $this);
        else
	        $form = new CourseTypeForm(CourseTypeForm :: TYPE_EDIT, $coursetype, $this->get_url(), $this);

        if ($form->validate())
        {
	        $success = $form->save_course_type();
	        $array_type = array();
	        $array_type['go'] = WeblcmsManager :: ACTION_ADMIN_COURSE_TYPE_BROWSER;
	        if($success ||  $form->get_form_type() == CourseTypeForm :: TYPE_EDIT)
	        	$array_type['course_type'] = $coursetype->get_id();
            $this->redirect(Translation :: get($success ? 'CourseTypeSaved' : 'CourseTypeNotSaved'), ($success ? false : true), $array_type );
            //$this->redirect(Translation :: get($success ? 'CourseTypeCreated' : 'CourseTypeNotCreated'), ($success ? false : true), array('go' => WeblcmsManager :: ACTION_ADMIN_COURSE_TYPE_BROWSER, 'course_type' => $coursetype->get_id()));
        }
        else
        {
            $this->display_header($trail, false, true);
            echo '<div class="clear">&nbsp;</div>';
            $form->display();
            $this->display_footer();
        }
    }
}
?>