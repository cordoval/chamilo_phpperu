<?php
/**
 * $Id: user_reporting_template.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.reporting.templates
 * @todo:
 * Template configuration:
 * Able to change name, description etc
 * 2 listboxes: one with available reporting blocks for the app, one with
 * reporting blocks already in template.
 */
require_once dirname (__FILE__) . '/../blocks/user_active_inactive_reporting_block.class.php';
require_once dirname (__FILE__) . '/../blocks/user_no_of_logins_day_reporting_block.class.php';
require_once dirname (__FILE__) . '/../blocks/user_no_of_logins_hour_reporting_block.class.php';
require_once dirname (__FILE__) . '/../blocks/user_no_of_logins_month_reporting_block.class.php';
require_once dirname (__FILE__) . '/../blocks/user_no_of_logins_reporting_block.class.php';
require_once dirname (__FILE__) . '/../blocks/user_no_of_users_picture_reporting_block.class.php';
require_once dirname (__FILE__) . '/../blocks/user_no_of_users_reporting_block.class.php';
require_once dirname (__FILE__) . '/../blocks/user_no_of_users_subscribed_course_reporting_block.class.php';


class UserReportingTemplate extends ReportingTemplate
{

    function UserReportingTemplate($parent)
    {
        //$this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("UserInformation"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_BLOCK_DIMENSIONS));
        //$this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("UserPlatformStatistics"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_BLOCK_DIMENSIONS));
        
        parent :: __construct($parent);
        
        $this->add_reporting_block(new UserActiveInactiveReportingBlock($this));
        $this->add_reporting_block(new UserNoOfLoginsDayReportingBlock($this));
        $this->add_reporting_block(new UserNoOfLoginsHourReportingBlock($this));
        $this->add_reporting_block(new UserNoOfLoginsMonthReportingBlock($this));
        $this->add_reporting_block(new UserNoOfLoginsReportingBlock($this));
        $this->add_reporting_block(new UserNoOfUsersPictureReportingBlock($this));
        $this->add_reporting_block(new UserNoOfUsersReportingBlock($this));
        $this->add_reporting_block(new UserNoOfUsersSubscribedCourseReportingBlock($this));
    }

    /**
     * @see ReportingTemplate -> get_properties()
     */
    /*public static function get_properties()
    {
        $properties[ReportingTemplateRegistration :: PROPERTY_TITLE] = 'UserReportingTemplateTitle';
        $properties[ReportingTemplateRegistration :: PROPERTY_PLATFORM] = 0;
        $properties[ReportingTemplateRegistration :: PROPERTY_DESCRIPTION] = 'UserReportingTemplateDescription';
        
        return $properties;
    }*/

    /**
     * @see ReportingTemplate -> to_html()
     */
    /*function to_html()
    {
        //template header
        $html[] = $this->get_header();
        
        $html[] = '<div class="reporting_template_container">';
        $html[] = '<div class="reporting_template_con_left">';
        $html[] = $this->get_reporting_block_html('UserInformation');
        $html[] = '</div>';
        $html[] = '<div class="reporting_template_con_right">';
        $html[] = $this->get_reporting_block_html('UserPlatformStatistics');
        $html[] = '</div><div class="clear">&nbsp;</div>';
        $html[] = '</div>';
        
        //show visible blocks
        //$html[] = $this->get_visible_reporting_blocks();
        

        //template footer
        $html[] = $this->get_footer();
        
        return implode("\n", $html);
    }*/
    
	function get_application()
    {
    	return UserManager::APPLICATION_NAME;
    }
    
    function display_context()
    {}
}
?>