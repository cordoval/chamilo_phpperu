<?php
/**
 * $Id: course_deleter.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/../weblcms_manager.class.php';

/**
 * Repository manager component which provides functionality to delete a course
 */
class WeblcmsManagerCourseDeleterComponent extends WeblcmsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
    	$wdm = WeblcmsDataManager :: get_instance();
        $course_codes = Request :: get(WeblcmsManager :: PARAM_COURSE);
        $failures = 0;
        
        if (! $this->get_user()->is_platform_admin())
        {
            $trail = BreadcrumbTrail :: get_instance();
            
            
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        if (! empty($course_codes))
        {
            if (! is_array($course_codes))
            {
                $course_codes = array($course_codes);
            }
            
            foreach ($course_codes as $course_code)
            {
                $course = $wdm->retrieve_course($course_code);
                
                if (! $course->delete())
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($course_codes) == 1)
                {
                    $message = 'SelectedCourseNotDeleted';
                }
                else
                {
                    $message = 'SelectedCoursesNotDeleted';
                }
            }
            else
            {
                if (count($course_codes) == 1)
                {
                    $message = 'SelectedCourseDeleted';
                }
                else
                {
                    $message = 'SelectedCoursesDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_BROWSER, WeblcmsManager :: PARAM_COURSE => null));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoCourseSelected')));
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {

        $breadcrumbtrail->add_help('weblcms_course_deleter');
    }

    function get_additional_parameters()
    {
        return array();
    }

}
?>