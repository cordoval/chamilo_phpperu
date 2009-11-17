<?php
/**
 * $Id: course_user_reporting_template.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.reporting.templates
 */
/**
 * @author Michael Kyndt
 * @todo:
 * Template configuration:
 * 2 listboxes: one with available reporting blocks for the app, one with
 * reporting blocks already in template.
 */
class CourseUserReportingTemplate extends ReportingTemplate
{

    function CourseUserReportingTemplate($parent, $id, $params)
    {
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("UserInformation"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_BLOCK_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("UserCourseStatistics"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_BLOCK_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("CourseInformation"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("CourseUserLearningpathInformation"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("CourseUserExerciseInformation"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));
        
        parent :: __construct($parent, $id, $params);
    }

    /**
     * @see ReportingTemplate -> get_properties()
     */
    public static function get_properties()
    {
        $properties[ReportingTemplateRegistration :: PROPERTY_TITLE] = 'CourseUserReportingTemplateTitle';
        $properties[ReportingTemplateRegistration :: PROPERTY_PLATFORM] = 0;
        $properties[ReportingTemplateRegistration :: PROPERTY_DESCRIPTION] = 'CourseUserReportingTemplateDescription';
        
        return $properties;
    }

    /**
     * @see ReportingTemplate -> to_html()
     */
    function to_html()
    {
        //template header
        $html[] = $this->get_header();
        
        //content
        $html[] = '<div class="reporting_template_container">';
        $html[] = '<div class="reporting_template_con_left">';
        $html[] = $this->get_reporting_block_html('UserInformation');
        $html[] = '</div>';
        $html[] = '<div class="reporting_template_con_right">';
        $html[] = $this->get_reporting_block_html('UserCourseStatistics');
        $html[] = '</div><div class="clear">&nbsp;</div>';
        $html[] = '</div>';
        
        $html[] = '<div class="reporting_template_container">';
        $html[] = $this->get_reporting_block_html('CourseInformation');
        $html[] = '</div>';
        
        $html[] = '<div class="clear">&nbsp;</div>';
        
        $html[] = '<div class="reporting_template_container">';
        $html[] = $this->get_reporting_block_html('CourseUserLearningpathInformation');
        $html[] = $this->get_reporting_block_html('CourseUserExerciseInformation');
        $html[] = '</div>';
        
        //template footer
        $html[] = $this->get_footer();
        
        return implode("\n", $html);
    }
}
?>