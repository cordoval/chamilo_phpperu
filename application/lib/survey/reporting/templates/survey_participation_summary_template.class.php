<?php
/**
 * $Id: survey_attempts_summary_template.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.reporting.templates
 */
require_once dirname(__FILE__) . '/../../survey_publication_category_menu.class.php';

class SurveyParticipationSummaryTemplate extends ReportingTemplate
{

    function SurveyParticipationSummaryTemplate($parent = null, $id, $params, $trail)
    {
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("SurveyParticipationSummary"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));
         parent :: __construct($parent, $id, $params, $trail);
    }

    /**
     * @see ReportingTemplate -> get_properties()
     */
    public static function get_properties()
    {
        $properties[ReportingTemplateRegistration :: PROPERTY_TITLE] = 'SurveyParticipationSummaryTemplateTitle';
        $properties[ReportingTemplateRegistration :: PROPERTY_PLATFORM] = 0;
        $properties[ReportingTemplateRegistration :: PROPERTY_DESCRIPTION] = 'SurveyParticipationSummaryTemplateDescription';
        
        return $properties;
    }

    /**
     * @see ReportingTemplate -> to_html()
     */
    function to_html()
    {
        //template header
        $html[] = $this->get_header();
        //$html[] = '<div class="reporting_center">';
        //show visible blocks
        
		        
        $current_category = Request :: get('category');
        $current_category = $current_category ? $current_category : 0;
        $menu = new SurveyPublicationCategoryMenu($current_category, '?application=survey&go=view_results&category=%s');
        
        $html[] = '<div style="float: left; width: 17%; overflow: auto;" />';
        $html[] = $menu->render_as_tree();
        $html[] = '</div>';
        
        $html[] = '<div style="float: right; width: 80%; overflow: auto;" />';
        $html[] = $this->get_visible_reporting_blocks();
        $html[] = '</div>';
        
        //$html[] = '</div>';
        //template footer
        $html[] = $this->get_footer();
        
        return implode("\n", $html);
    }
}
?>