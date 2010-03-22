<?php
/**
 * @author Michael Kyndt
 */
class AdminDataReportingTemplate extends ReportingTemplate
{

    function AdminDataReportingTemplate($parent)
    {
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("AdminNoOfApplications"));
        
        parent :: __construct($parent);
    }

    /**
     * @see ReportingTemplate -> get_properties()
     */
    /*public static function get_properties()
    {
        $properties[ReportingTemplateRegistration :: PROPERTY_TITLE] = 'AdminDataReportingTemplateTitle';
        $properties[ReportingTemplateRegistration :: PROPERTY_PLATFORM] = 1;
        $properties[ReportingTemplateRegistration :: PROPERTY_DESCRIPTION] = 'AdminDataReportingTemplateDescription';
        
        return $properties;
    }*/

    /**
     * @see ReportingTemplate -> to_html()
     */
    /*function to_html()
    {
        //template header
        /*$html[] = $this->get_header();
        
        //template menu
        //$html[] = $this->get_menu();
        

        //show visible blocks
        $html[] = $this->get_visible_reporting_blocks();
        
        //template footer
        $html[] = $this->get_footer();
        
        return implode("\n", $html);*/
    	/*$html[] = $this->display_header();
		$html[] = $this->get_menu();
		$html[] = $this->display_context();
		$html[] = $this->render_blocks();
		$html[] =  $this->display_footer();
		return implode("\n", $html);
    }*/
    
    function get_application()
    {
    	return SurveyManager::APPLICATION_NAME;
    }
    
    function display_context()
    {
    	
    }
}
?>