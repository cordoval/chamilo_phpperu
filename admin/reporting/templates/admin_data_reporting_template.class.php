<?php
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__) . '/../blocks/admin_no_of_applications_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/admin_most_used_web_applications_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/admin_most_used_core_applications_reporting_block.class.php';

class AdminDataReportingTemplate extends ReportingTemplate
{

    function AdminDataReportingTemplate($parent)
    {
        parent :: __construct($parent);
    	$this->add_reporting_block(new AdminNoOfApplicationsReportingBlock($this));
    	$this->add_reporting_block(new AdminMostUsedWebApplicationsReportingBlock($this));
    	$this->add_reporting_block(new AdminMostUsedCoreApplicationsReportingBlock($this));
    }
    
    function get_application()
    {
    	return AdminManager::APPLICATION_NAME;
    }
    
    function display_context()
    {
    	
    }

	function is_platform()
    {
    	return true;
    }
}
?>