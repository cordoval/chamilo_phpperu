<?php
/**
 * $Id: login_data_reporting_template.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.reporting.templates
 * Template configuration:
 * Able to change name, description etc
 * 2 listboxes: one with available reporting blocks for the app, one with
 * reporting blocks already in template.
 */
class LoginDataReportingTemplate extends ReportingTemplate
{

    function LoginDataReportingTemplate($parent, $id, $params)
    {
        parent :: __construct($parent);
    	
    	//$this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("ActiveInactive"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_INVISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_BLOCK_DIMENSIONS));
        //$this->add_reporting_block(new UserActiveInactiveBlockReportingBlock($this));
        /*$this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("UserNoOfUsers"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_INVISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_BLOCK_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("UserNoOfLogins"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_INVISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_BLOCK_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("UserNoOfLoginsMonth"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_INVISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_BLOCK_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("UserNoOfLoginsDay"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_INVISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_BLOCK_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("UserNoOfLoginsHour"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_INVISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_BLOCK_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("UserNoOfUsersPicture"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_INVISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_BLOCK_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("UserNoOfUsersSubscribedCourse"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_INVISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_BLOCK_DIMENSIONS));*/
        
        
    }

    /**
     * @see ReportingTemplate -> get_properties()
     */
    public static function get_properties()
    {
        $properties[ReportingTemplateRegistration :: PROPERTY_TITLE] = 'LoginDataReportingTemplateTitle';
        $properties[ReportingTemplateRegistration :: PROPERTY_PLATFORM] = 1;
        $properties[ReportingTemplateRegistration :: PROPERTY_DESCRIPTION] = 'LoginDataReportingTemplateDescription';
        
        return $properties;
    }

    /**
     * @see ReportingTemplate -> to_html()
     */
    function to_html()
    {
        //template header
        $html[] = $this->get_header();
        
        //template menu
        $html[] = $this->get_menu();
        
        //show visible blocks
        $html[] = $this->get_visible_reporting_blocks();
        
        //template footer
        $html[] = $this->get_footer();
        
        return implode("\n", $html);
    }
    
    function get_application()
    {
    	return UserManager::APPLICATION_NAME;
    }
    
    function display_context()
    {}
}
?>