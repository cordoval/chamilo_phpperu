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
        parent :: __construct($parent);
        
        $this->add_reporting_block(new UserActiveInactiveReportingBlock($this));
        $this->add_reporting_block(new UserNoOfLoginsDayReportingBlock($this));
        $this->add_reporting_block(new UserNoOfLoginsHourReportingBlock($this));
        $this->add_reporting_block(new UserNoOfLoginsMonthReportingBlock($this));
        $this->add_reporting_block(new UserNoOfLoginsReportingBlock($this));
        $this->add_reporting_block(new UserNoOfUsersPictureReportingBlock($this));
        $this->add_reporting_block(new UserNoOfUsersReportingBlock($this));
       // $this->add_reporting_block(new UserNoOfUsersSubscribedCourseReportingBlock($this));
    }
   
	function get_application()
    {
    	return UserManager::APPLICATION_NAME;
    }
    
    function display_context()
    {}
}
?>