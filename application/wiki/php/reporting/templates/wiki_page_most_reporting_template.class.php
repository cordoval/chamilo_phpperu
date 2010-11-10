<?php
namespace application\wiki;

use reporting\ReportingTemplate;
use reporting\ReportingTemplateRegistration;
/**
 * $Id: wiki_page_reporting_template.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.reporting.templates
 */
/**
 * @author Michael Kyndt
 */

class WikiPageMostReportingTemplate extends ReportingTemplate
{

    function WikiPageMostReportingTemplate($parent, $id, $params)
    {
        $this->add_reporting_block(new WikiPageMostActiveUsersReportingBlock($this));
        $this->add_reporting_block(new WikiPageUsersContributionsReportingBlock($this));
        
        parent :: __construct($parent);
    }
    
	function display_context()
	{
		//publicatie, content_object, application ... 
	}
	
	function get_application()
    {
    	return WikiManager::APPLICATION_NAME;
    }
    
    /**
     * @see ReportingTemplate -> get_properties()
     */
    public static function get_properties()
    {
        $properties[ReportingTemplateRegistration :: PROPERTY_TITLE] = 'WeblcmsWikiPageReportingTemplate';
        $properties[ReportingTemplateRegistration :: PROPERTY_PLATFORM] = 0;
        $properties[ReportingTemplateRegistration :: PROPERTY_DESCRIPTION] = 'WeblcmsWikiPageReportingTemplate';
        
        return $properties;
    }

    /**
     * @see ReportingTemplate -> to_html()
     */
  /*  function to_html()
    {
        //template header
        $html[] = $this->get_header();
        $html[] = $this->get_visible_reporting_blocks();
        
        //template footer
        $html[] = $this->get_footer();
        
        return implode("\n", $html);
    }*/
}
?>