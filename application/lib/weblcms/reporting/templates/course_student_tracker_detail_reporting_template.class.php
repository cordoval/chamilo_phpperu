<?php
/**
 * $Id: course_student_tracker_detail_reporting_template.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.reporting.templates
 */
/**
 * @author Michael Kyndt
 */
class CourseStudentTrackerDetailReportingTemplate extends ReportingTemplate
{

    function CourseStudentTrackerDetailReportingTemplate($parent, $id, $params)
    {
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("UserInformation"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_BLOCK_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("UserCourseStatistics"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_BLOCK_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("CourseInformation"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("CourseUserLearningpathInformation"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("CourseUserExerciseInformation"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("WeblcmsLastAccessToTools"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("WeblcmsLatestAccess"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));
        
        parent :: __construct($parent, $id, $params);
    }
    
	function display_context()
	{
		//publicatie, content_object, application ... 
	}
	
	function get_application()
    {
    	return WeblcmsManager::APPLICATION_NAME;
    }
    
    /**
     * @see ReportingTemplate -> get_properties()
     */
    public static function get_properties()
    {
        $properties[ReportingTemplateRegistration :: PROPERTY_TITLE] = 'CourseStudentTrackerDetailReportingTemplateTitle';
        $properties[ReportingTemplateRegistration :: PROPERTY_PLATFORM] = 0;
        $properties[ReportingTemplateRegistration :: PROPERTY_DESCRIPTION] = 'CourseStudentTrackerDetailReportingTemplateDescription';
        
        return $properties;
    }

    /**
     * @see ReportingTemplate -> to_html()
     */
    function to_html()
    {
        //template header
        $html[] = $this->get_header();
        
        $html[] = '<div class="reporting_template_container">';
        $html[] = '<div class="reporting_template_con_left">';
        $html[] = $this->get_reporting_block_html('UserInformation');
        $html[] = '</div>';
        $html[] = '<div class="reporting_template_con_right">';
        $html[] = $this->get_reporting_block_html('UserCourseStatistics');
        $html[] = '</div><div class="clear">&nbsp;</div>';
        $html[] = '</div>';
        $html[] = '<br />';
        
        $html[] = '<div class="reporting_template_container">';
        $html[] = $this->get_reporting_block_html('CourseInformation') . '<br />';
        $html[] = $this->get_reporting_block_html('CourseUserLearningpathInformation') . '<br />';
        $html[] = $this->get_reporting_block_html('CourseUserExerciseInformation') . '<br />';
        $html[] = $this->get_reporting_block_html('WeblcmsLastAccessToTools') . '<br />';
        $html[] = $this->get_reporting_block_html('WeblcmsLatestAccess'); //.'<br />';
        $html[] = '</div>';
        //template menu
        //$html[] = $this->get_menu();
        

        //show visible blocks
        //$html[] = $this->get_visible_reporting_blocks();
        

        //template footer
        $html[] = $this->get_footer();
        
        return implode("\n", $html);
    }
}
?>