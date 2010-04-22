<?php
/**
 * $Id: course_type_deleter.class.php 218 2010-03-15 10:30:26Z Yannick $
 * @package application.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/../weblcms_manager.class.php';
require_once dirname(__FILE__) . '/../weblcms_manager_component.class.php';
/**
 * Repository manager component which provides functionality to delete a course_type
 */
class WeblcmsManagerCourseTypeCoursesDeleterComponent extends WeblcmsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $course_type_id = Request :: get(WeblcmsManager :: PARAM_COURSE_TYPE);
        $failures = 0;
        
        if (! $this->get_user()->is_platform_admin())
        {
            $trail = new BreadcrumbTrail();
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('DeleteCoursesByCourseType')));
            $trail->add_help('course_type courses delete');
            
            $this->display_header($trail, false, true);
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        if (! empty($course_type_id))
        {

        	$wdm = WeblcmsDataManager::get_instance();
        	$result = $wdm->delete_courses_by_course_type_id($course_type_id);
            if ($result)
	            $message = 'AllSelectedCoursesDeleted';
			else
                $message = 'NotAllSelectedCoursesDeleted';
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_VIEW_COURSE_TYPE, WeblcmsManager :: PARAM_COURSE_TYPE => $course_type_id));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoCourseTypeSelected')));
        }
    }
}
?>