<?php
/**
 * $Id: course_data_reporting_template.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.reporting.templates
 */
/**
 * @author Michael Kyndt
 * @todo:
 * Template configuration:
 * Able to change name, description etc
 * 2 listboxes: one with available reporting blocks for the app, one with
 * reporting blocks already in template.
 */

require_once (__FILE__) . '/blocks/weblcms_no_of_courses_reporting_block.class.php';
require_once (__FILE__) . '/blocks/weblcms_most_active_inactive_last_visit_reporting_block.class.php';
require_once (__FILE__) . '/blocks/weblcms_most_active_inactive_last_publication_reporting_block.class.php';
require_once (__FILE__) . '/blocks/weblcms_most_active_inactive_last_detail_reporting_block.class.php';
require_once (__FILE__) . '/blocks/weblcms_no_of_published_objects_per_type_reporting_block.class.php';
require_once (__FILE__) . '/blocks/weblcms_no_of_objects_per_type_reporting_block.class.php';
require_once (__FILE__) . '/../../../user/reporting/blocks/user_no_of_users_subscribed_course_reporting_block.class.php';

class CourseDataReportingTemplate extends ReportingTemplate
{
    function CourseDataReportingTemplate($parent)
    {
        parent :: __construct($parent);
        $this->add_reporting_block(new WeblcmsNoOfCoursesReportingBlock($this));
        $this->add_reporting_block(new WeblcmsNoOfCourseByLanguageReportingBlock($this));
        $this->add_reporting_block(new WeblcmsMostActiveInactiveLastVisitReportingBlock($this));
        $this->add_reporting_block(new WeblcmsMostActiveInactiveLastPublicationReportingBlock($this));
        $this->add_reporting_block(new WeblcmsMostActiveInactiveLastDetailReportingBlock($this));
        $this->add_reporting_block(new WeblcmsNoOfPublishedObjectsPerTypeReportingBlock($this));
        $this->add_reporting_block(new WeblcmsNoOfObjectsPerTypeReportingBlock($this));
        $this->add_reporting_block(new WeblcmsCoursePerCategoryReportingBlock($this));
        $this->add_reporting_block(new WeblcmslastAccessToToolsPlatformReportingBlock($this));
        $this->add_reporting_block(new UserNoOfUsersSubscribedCourseReportingBlock($this));
        /*$this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("WeblcmsNoOfCourses"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_INVISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_BLOCK_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("WeblcmsNoOfCoursesByLanguage"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_INVISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_BLOCK_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("WeblcmsMostActiveInactiveLastVisit"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_INVISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("WeblcmsMostActiveInactiveLastPublication"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_INVISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("WeblcmsMostActiveInactiveDetail"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_INVISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("UserNoOfUsersSubscribedCourse"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_INVISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_BLOCK_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("WeblcmsNoOfPublishedObjectsPerType"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_INVISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_BLOCK_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("WeblcmsNoOfObjectsPerType"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_INVISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_BLOCK_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("WeblcmsCoursesPerCategory"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_INVISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_BLOCK_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("WeblcmsLastAccessToToolsPlatform"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_INVISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));
        
        parent :: __construct($parent, $id, $params);*/
    }

	function display_context()
	{

	}
	
	function get_application()
    {
    	return WeblcmsManager::APPLICATION_NAME;
    }
}
?>