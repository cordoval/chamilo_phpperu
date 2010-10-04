<?php
require_once dirname(__FILE__) . '/../blocks/user_no_of_logins_day_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/user_no_of_logins_hour_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/user_no_of_logins_month_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/user_no_of_logins_reporting_block.class.php';

class UserLoginsReportingTemplate extends ReportingTemplate
{

    function UserLoginsReportingTemplate($parent)
    {
        parent :: __construct($parent);
        $user_id = Request :: get(UserManager::PARAM_USER_USER_ID);
		$this->set_parameter(UserManager::PARAM_USER_USER_ID, $user_id);
        $this->add_reporting_block(new UserNoOfLoginsDayReportingBlock($this));
        $this->add_reporting_block(new UserNoOfLoginsHourReportingBlock($this));
        $this->add_reporting_block(new UserNoOfLoginsMonthReportingBlock($this));
        $this->add_reporting_block(new UserNoOfLoginsReportingBlock($this));
    }

    function get_application()
    {
        return UserManager :: APPLICATION_NAME;
    }

    function display_context()
    {
    }    
}
?>