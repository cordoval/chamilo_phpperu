<?php
/**
 * $Id: learning_path_attempts_reporting_template.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.reporting.templates
 */
/**
 * @author Michael Kyndt
 */

class LearningPathAttemptsReportingTemplate extends ReportingTemplate
{
    private $object;

    function LearningPathAttemptsReportingTemplate($parent = null, $id, $params)
    {
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("WeblcmsLearningPathAttempts"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));
        
        parent :: __construct($parent, $id, $params);
    }

    /**
     * @see ReportingTemplate -> get_properties()
     */
    public static function get_properties()
    {
        $properties[ReportingTemplateRegistration :: PROPERTY_TITLE] = 'LearningPathAttemptsReportingTemplateTitle';
        $properties[ReportingTemplateRegistration :: PROPERTY_PLATFORM] = 0;
        $properties[ReportingTemplateRegistration :: PROPERTY_DESCRIPTION] = 'LearningPathAttemptsReportingTemplateDescription';
        
        return $properties;
    }

    /**
     * @see ReportingTemplate -> to_html()
     */
    function to_html()
    {
        //template header
        $html[] = $this->get_header();
        
        //$html[] = '<div align="center">';
        //show visible blocks
        $html[] = $this->get_visible_reporting_blocks();
        //$html[] = '</div>';
        //template footer
        $html[] = $this->get_footer();
        
        return implode("\n", $html);
    }
}
?>