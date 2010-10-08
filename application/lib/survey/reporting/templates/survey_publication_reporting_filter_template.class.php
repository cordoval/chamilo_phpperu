<?php
require_once PATH :: get_reporting_path() . '/lib/reporting_template.class.php';
require_once dirname(__FILE__) . '/../blocks/survey_participant_reporting_filter_block.class.php';
require_once dirname(__FILE__) . '/../blocks/survey_participant_mail_reporting_filter_block.class.php';
require_once dirname(__FILE__) . '/../blocks/survey_question_type_reporting_filter_block.class.php';
require_once dirname(__FILE__) . '/../blocks/survey_context_template_reporting_filter_block.class.php';
require_once dirname(__FILE__) . '/../blocks/survey_context_reporting_filter_block.class.php';
require_once Path :: get_application_path() . 'lib/survey/wizards/survey_reporting_filter_wizard.class.php';

class SurveyPublicationReportingFilterTemplate extends ReportingTemplate
{
	private $filter_parameters;
	private $wizard;
	function SurveyPublicationReportingFilterTemplate($parent)
	{
		parent :: __construct($parent);
		$this->add_reporting_block(new SurveyParticipantReportingFilterBlock($this));
		$this->add_reporting_block(new SurveyParticipantMailReportingFilterBlock($this));		
		$this->add_reporting_block(new SurveyQuestionTypeReportingFilterBlock($this));
		$this->add_reporting_block(new SurveyContextTemplateReportingFilterBlock($this));
		$this->add_reporting_block(new SurveyContextReportingFilterBlock($this));
	}
	
	public function display_filter()
	{
		$html = array();
		$ids = Request :: get(SurveyManager :: PARAM_PUBLICATION_ID);
		Request::set_get(DynamicFormTabsRenderer::PARAM_SELECTED_TAB, Request::post('submit'));
		$this->wizard = new SurveyReportingFilterWizard(SurveyReportingFilterWizard::TYPE_CONTEXTS,$ids, $this->get_url($this->get_parameters()));
		
		if($this->wizard->validate())
		{
			$this->filter_parameters = $this->wizard->getParameters();
		}
		
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
	
	function get_filter_parameters()
	{
		return $this->filter_parameters;
	}

}
?>