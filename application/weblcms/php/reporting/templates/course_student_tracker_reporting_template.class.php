<?php
/**
 * $Id: course_student_tracker_reporting_template.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.reporting.templates
 */
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__) . '/../blocks/weblcms_users_tracking_reporting_block.class.php';
require_once dirname(__FILE__) . '/../../weblcms_manager/weblcms_manager.class.php';

class CourseStudentTrackerReportingTemplate extends ReportingTemplate
{

    function CourseStudentTrackerReportingTemplate($parent)
    {
        parent :: __construct($parent);
    	//$this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("UserTracking"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));
        $this->add_reporting_block($this->get_users_tracking());
        
    }
    
	function display_context()
	{
		//publicatie, content_object, application ... 
	}
	
	function get_application()
    {
    	return WeblcmsManager::APPLICATION_NAME;
    }
    
    function get_users_tracking()
    {
    	$course_weblcms_block = new WeblcmsUsersTrackingReportingBlock($this);
    	$course_id = Request :: get(WeblcmsManager::PARAM_COURSE);
    	if ($course_id)
    	{
    		$this->set_parameter(WeblcmsManager::PARAM_COURSE, $course_id);
    	}
    	return $course_weblcms_block;
    }
}
?>