<?php
/**
 * $Id: course_type_deleter.class.php 218 2010-03-15 10:30:26Z Yannick $
 * @package application.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/../weblcms_manager.class.php';

/**
 * Repository manager component which provides functionality to delete a course_type
 */
class WeblcmsManagerCourseTypeDeleterComponent extends WeblcmsManager
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
            $trail = BreadcrumbTrail :: get_instance();
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('DeleteCourseType')));
            $trail->add_help('course_type delete');
            
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        if (! empty($course_type_id))
        {
        	$wdm = WeblcmsDataManager::get_instance();
            if (! is_array($course_type_id))
            {
                $course_type_id = array($course_type_id);
            }
            
            foreach ($course_type_id as $course_type_id)
            {                
                if (! $wdm->delete_course_type($course_type_id))
                {
                    $failures ++;
                }
                
                $condition = new EqualityCondition(Course :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
           		$properties = array(Course :: PROPERTY_COURSE_TYPE_ID => 0);
              	if(!$wdm->update_courses($properties, $condition))
                	$failures ++;
                
               	$condition = new EqualityCondition(CourseTypeUserCategory :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
               	$course_type_user_categories = $wdm->retrieve_course_type_user_categories($condition);
               	while($category = $course_type_user_categories->next_result())
               	{
               		if(!$category->delete())
               			$failures ++;
               	} 
            }
            
            if ($failures)
            {
                if (count($course_type_id) == 1)
                {
                    $message = 'SelectedCourseTypeNotDeleted';
                }
                else
                {
                    $message = 'SelectedCourseTypesNotDeleted';
                }
            }
            else
            {
                if (count($course_type_id) == 1)
                {
                    $message = 'SelectedCourseTypeDeleted';
                }
                else
                {
                    $message = 'SelectedCourseTypesDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_TYPE_BROWSER, WeblcmsManager :: PARAM_COURSE_TYPE => null));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoCourseTypeSelected')));
        }
    }
}
?>