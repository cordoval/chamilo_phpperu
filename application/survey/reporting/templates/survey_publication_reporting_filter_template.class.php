<?php
require_once PATH :: get_reporting_path() . '/lib/reporting_template.class.php';
require_once dirname(__FILE__) . '/../blocks/survey_participant_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/survey_participant_mail_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/survey_question_type_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/survey_context_template_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/survey_context_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/survey_question_reporting_block.class.php';
require_once Path :: get_application_path() . 'lib/survey/wizards/survey_reporting_filter_wizard.class.php';

class SurveyPublicationReportingFilterTemplate extends ReportingTemplate
{
	private $wizard;
	function SurveyPublicationReportingFilterTemplate($parent)
	{
		parent :: __construct($parent);
		$this->add_reporting_block(new SurveyParticipantReportingBlock($this));
		$this->add_reporting_block(new SurveyParticipantMailReportingBlock($this));		
		$this->add_reporting_block(new SurveyQuestionTypeReportingBlock($this));
		//$this->add_reporting_block(new SurveyContextTemplateReportingBlock($this));
		//$this->add_reporting_block(new SurveyContextReportingBlock($this));
		$this->add_reporting_block(new SurveyQuestionReportingBlock($this));
	}
	
	public function display_filter()
	{
		$html = array();
		$ids = Request :: get(SurveyManager :: PARAM_PUBLICATION_ID);
		$this->wizard = new SurveyReportingFilterWizard($ids, $this->get_url($parameters));
		$html[] = $this->reporting_filter_header();
		$html[] = $this->wizard->toHtml();
		$html[] = $this->reporting_filter_footer();
		return implode("\n", $html);
	}
	
	public function display_context()
	{
		
	}
	
	function reporting_filter_header()
    {
    	$html = array();
    	
       	$html[] = '<div style="clear: both; height: 0px; line-height: 0px;">&nbsp;</div>';
        $html[] = '<div id="reporting_filter" class="reporting_filter">';
        $html[] = '<div class="bevel">';
       	
        $html[] = '<div class="clear"></div>';
        return implode("\n", $html); 
    }
    
    function reporting_filter_footer()
    {
    	$html = array();
        
       $html[] = '<div class="clear"></div>';
        $html[] = '<div id="reporting_filter_hide_container" class="reporting_filter_hide_container">';
        $html[] = '<a id="reporting_filter_hide_link" class="reporting_filter_hide" href="#"><img src="' . Theme :: get_common_image_path() . 'action_ajax_hide.png" /></a>';
        $html[] = '</div>';
		$html[] = '</div>';
		$html[] = '</div>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/reporting_filter_horizontal.js');

        $html[] = '<div class="clear"></div>';
        
        return implode("\n", $html); 
    }
	
	function get_application()
	{
		return SurveyManager::APPLICATION_NAME;
	}

}
?>