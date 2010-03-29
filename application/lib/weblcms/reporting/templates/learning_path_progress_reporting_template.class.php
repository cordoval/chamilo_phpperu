<?php
/**
 * $Id: learning_path_progress_reporting_template.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.reporting.templates
 */
/**
 * @author Michael Kyndt
 */
class LearningPathProgressReportingTemplate extends ReportingTemplate
{
    private $object;

    function LearningPathProgressReportingTemplate($parent = null, $id, $params, $trail, $object)
    {
        $this->object = $object;
        
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("WeblcmsLearningPathProgress"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));
        
        parent :: __construct($parent, $id, $params, $trail);
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
        $properties[ReportingTemplateRegistration :: PROPERTY_TITLE] = 'LearningPathProgressReportingTemplateTitle';
        $properties[ReportingTemplateRegistration :: PROPERTY_PLATFORM] = 0;
        $properties[ReportingTemplateRegistration :: PROPERTY_DESCRIPTION] = 'LearningPathProgressReportingTemplateDescription';
        
        return $properties;
    }

    /**
     * @see ReportingTemplate -> to_html()
     */
    function to_html()
    {
        //template header
        $html[] = $this->get_header();
        
        if (Request :: get('cid'))
        {
            $display = ContentObjectDisplay :: factory($this->object);
            $html[] = $display->get_full_html();
        }
        
        //$html[] = '<div class="reporting_center">';
        //show visible blocks
        $html[] = $this->get_visible_reporting_blocks();
        //$html[] = '</div>';
        //template footer
        $html[] = $this->get_footer();
        
        return implode("\n", $html);
    }
}
?>