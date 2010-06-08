<?php
/**
 * $Id: user_data_reporting_template.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.reporting.templates
 * @todo:
 * Template configuration:
 * Able to change name, description etc
 * 2 listboxes: one with available reporting blocks for the app, one with
 * reporting blocks already in template.
 */
require_once dirname (__FILE__) . '/../blocks/browsers_reporting_block.class.php';
require_once dirname (__FILE__) . '/../blocks/os_reporting_block.class.php';
require_once dirname (__FILE__) . '/../blocks/countries_reporting_block.class.php';
require_once dirname (__FILE__) . '/../blocks/providers_reporting_block.class.php';
require_once dirname (__FILE__) . '/../blocks/referers_reporting_block.class.php';


class UserDataReportingTemplate extends ReportingTemplate
{

    function UserDataReportingTemplate($parent)
    {
         parent :: __construct($parent);
        
         $this->add_reporting_block(new BrowsersReportingBlock($this));
         $this->add_reporting_block(new CountriesReportingBlock($this));
         $this->add_reporting_block(new OsReportingBlock($this));
         $this->add_reporting_block(new ProvidersReportingBlock($this));
         $this->add_reporting_block(new ReferersReportingBlock($this));
    }
    
	function get_application()
    {
    	return UserManager::APPLICATION_NAME;
    }
    
    function display_context()
    {}
    
	function is_platform()
    {
    	return true;
    }
}
?>