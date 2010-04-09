<?php
/**
 * $Id: course_data_reporting_template.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.reporting.templates
 */
require_once dirname(__FILE__) . '/../blocks/weblcms_no_of_courses_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/weblcms_no_of_courses_by_language_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/weblcms_courses_per_category_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/weblcms_most_active_inactive_last_visit_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/weblcms_most_active_inactive_last_publication_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/weblcms_most_active_inactive_last_detail_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/weblcms_no_of_published_objects_per_type_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/weblcms_no_of_objects_per_type_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/weblcms_last_access_to_tools_platform_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/weblcms_no_of_users_subscribed_course_reporting_block.class.php';
require_once dirname(__FILE__) . '/../../weblcms_manager/weblcms_manager.class.php';

class CourseDataReportingTemplate extends ReportingTemplate
{
    function CourseDataReportingTemplate($parent)
    {
        parent :: __construct($parent);
        $this->add_reporting_block($this->get_no_of_courses());
        $this->add_reporting_block($this->get_no_of_courses_by_language());
        $this->add_reporting_block($this->get_most_active_inactive_last_visit());
        $this->add_reporting_block($this->get_most_active_inactive_last_publication());
        $this->add_reporting_block($this->get_most_active_inactive_last_detail());
        //$this->add_reporting_block($this->get_no_of_objects_per_type());
        //$this->add_reporting_block($this->get_no_of_published_objects_per_type());
        $this->add_reporting_block($this->get_courses_per_category());
        $this->add_reporting_block($this->get_last_access_to_tools_platform());
		$this->add_reporting_block($this->get_no_of_users());
    }

	function display_context()
	{

	}
	
	function get_application()
    {
    	return WeblcmsManager::APPLICATION_NAME;
    }
    
    function is_platform()
    {
    	return true;
    }
    
    function get_no_of_courses()
    {
    	$course_weblcms_block = new WeblcmsNoOfCoursesReportingBlock($this);
//    	$course_id = Request :: get(WeblcmsManager::PARAM_COURSE);
//    	if ($course_id)
//    	{
//    		$course_weblcms_block->set_course_id($course_id);
//    		$this->add_parameters(WeblcmsManager::PARAM_COURSE, $course_id);
//    	}
    	return $course_weblcms_block;
    }
    
    function get_no_of_courses_by_language()
    {
    	$course_weblcms_block = new WeblcmsNoOfCoursesByLanguageReportingBlock($this);
//    	$course_id = Request :: get(WeblcmsManager::PARAM_COURSE);
//    	if ($course_id)
//    	{
//    		$course_weblcms_block->set_course_id($course_id);
//    		$this->add_parameters(WeblcmsManager::PARAM_COURSE, $course_id);
//    	}
    	return $course_weblcms_block;
    }
    
    function get_most_active_inactive_last_visit()
    {
        $course_weblcms_block = new WeblcmsMostActiveInactiveLastVisitReportingBlock($this);
//    	$course_id = Request :: get(WeblcmsManager::PARAM_COURSE);
//    	if ($course_id)
//    	{
//    		$course_weblcms_block->set_course_id($course_id);
//    		$this->add_parameters(WeblcmsManager::PARAM_COURSE, $course_id);
//    	}   
    	return $course_weblcms_block;	
    }
    
    function get_most_active_inactive_last_publication()
    {
        $course_weblcms_block = new WeblcmsMostActiveInactiveLastPublicationReportingBlock($this);
//    	$course_id = Request :: get(WeblcmsManager::PARAM_COURSE);
//    	if ($course_id)
//    	{
//    		$course_weblcms_block->set_course_id($course_id);
//    		$this->add_parameters(WeblcmsManager::PARAM_COURSE, $course_id);
//    	}
    	return $course_weblcms_block;
    }
    
    function get_most_active_inactive_last_detail()
    {
        $course_weblcms_block = new WeblcmsMostActiveInactiveLastDetailReportingBlock($this);
//    	$course_id = Request :: get(WeblcmsManager::PARAM_COURSE);
//    	if ($course_id)
//    	{
//    		$course_weblcms_block->set_course_id($course_id);
//    		$this->add_parameters(WeblcmsManager::PARAM_COURSE, $course_id);
//    	}
    	return $course_weblcms_block;
    }
    
    function get_no_of_objects_per_type()
    {
        $course_weblcms_block = new WeblcmsNoOfObjectsPerTypeReportingBlock($this);
//    	$course_id = Request :: get(WeblcmsManager::PARAM_COURSE);
//    	if ($course_id)
//    	{
//    		$course_weblcms_block->set_course_id($course_id);
//    		$this->add_parameters(WeblcmsManager::PARAM_COURSE, $course_id);
//    	}
    	return $course_weblcms_block;
    }
    
    function get_no_of_published_objects_per_type()
    {
    $course_weblcms_block = new WeblcmsNoOfPublishedObjectsPerTypeReportingBlock($this);
//    	$course_id = Request :: get(WeblcmsManager::PARAM_COURSE);
//    	if ($course_id)
//    	{
//    		$course_weblcms_block->set_course_id($course_id);
//    		$this->add_parameters(WeblcmsManager::PARAM_COURSE, $course_id);
//    	}
    	return $course_weblcms_block;
    }
    
    function get_courses_per_category()
    {
        $course_weblcms_block = new WeblcmsCoursesPerCategoryReportingBlock($this);
//    	$course_id = Request :: get(WeblcmsManager::PARAM_COURSE);
//    	if ($course_id)
//    	{
//    		$course_weblcms_block->set_course_id($course_id);
//    		$this->add_parameters(WeblcmsManager::PARAM_COURSE, $course_id);
//    	}
    	return $course_weblcms_block;
    }
    
    function get_last_access_to_tools_platform()
    {
        $course_weblcms_block = new WeblcmsLastAccessToToolsPlatformReportingBlock($this);
//    	$course_id = Request :: get(WeblcmsManager::PARAM_COURSE);
//    	if ($course_id)
//    	{
//    		$course_weblcms_block->set_course_id($course_id);
//    		$this->add_parameters(WeblcmsManager::PARAM_COURSE, $course_id);
//    	}
    	return $course_weblcms_block;
    }
    
	function get_no_of_users()
    {
        $course_weblcms_block = new WeblcmsNoOfUsersSubscribedCourseReportingBlock($this);
//    	$course_id = Request :: get(WeblcmsManager::PARAM_COURSE);
//    	if ($course_id)
//    	{
//    		$course_weblcms_block->set_course_id($course_id);
//    		$this->add_parameters(WeblcmsManager::PARAM_COURSE, $course_id);
//    	}
    	return $course_weblcms_block;
    }
}
?>