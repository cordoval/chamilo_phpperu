<?php

namespace application\assessment;

use reporting\ReportingTemplate;
use reporting\ReportingTemplateRegistration;
use common\libraries\Request;
/**
 * $Id: assessment_attempts_summary_template.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.reporting.templates
 */
require_once dirname(__FILE__) . '/../../lib/assessment_publication_category_menu.class.php';
require_once dirname(__FILE__) . '/../blocks/assessment_attempts_summary_reporting_block.class.php';

class AssessmentAttemptsSummaryTemplate extends ReportingTemplate
{

    function __construct($parent = null, $id, $params, $trail)
    {
    	
        $block = new AssessmentAttemptsSummaryReportingBlock($this);
        $block->set_function_parameters($params);
        $this->add_reporting_block($block);
        
        parent :: __construct($parent, $id, $params, $trail);
    }
    
	function display_context()
	{
		//publicatie, content_object, application ... 
	}
	
    function get_application()
    {
    	return AssessmentManager::APPLICATION_NAME;
    }
	
    /**
     * @see ReportingTemplate -> get_properties()
     */
    public static function get_properties()
    {
        $properties[ReportingTemplateRegistration :: PROPERTY_TITLE] = 'AssessmentAttemptsSummaryTemplateTitle';
        $properties[ReportingTemplateRegistration :: PROPERTY_PLATFORM] = 0;
        $properties[ReportingTemplateRegistration :: PROPERTY_DESCRIPTION] = 'AssessmentAttemptsSummaryTemplateDescription';
        
        return $properties;
    }

    /**
     * @see ReportingTemplate -> to_html()
     */
    function to_html()
    {
        //template header
        $html[] = $this->display_header();
        //$html[] = '<div class="reporting_center">';
        //show visible blocks
        

        $current_category = Request :: get('category');
        $current_category = $current_category ? $current_category : 0;
        $menu = new AssessmentPublicationCategoryMenu($current_category, '?application=assessment&go=view_apub_results&category=%s');
        
        $html[] = '<div style="float: left; width: 17%; overflow: auto;" />';
        $html[] = $menu->render_as_tree();
        $html[] = '</div>';
        
        $html[] = '<div style="float: right; width: 80%; overflow: auto;" />';
        $html[] = $this->render_all_blocks();
        $html[] = '</div>';
        
        //$html[] = '</div>';
        //template footer
        $html[] = $this->display_footer();
        
        return implode("\n", $html);
    }
}
?>