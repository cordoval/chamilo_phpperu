<?php
require_once PATH :: get_reporting_path() . '/lib/reporting_template.class.php';
require_once dirname(__FILE__) . '/../blocks/survey_participant_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/survey_participant_mail_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/survey_question_type_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/survey_context_template_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/survey_context_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/survey_question_reporting_block.class.php';

class SurveyPublicationReportingFilterTemplate extends ReportingTemplate
{
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
	
	public function display_context()
	{
		
	}
	
	function get_application()
	{
		return SurveyManager::APPLICATION_NAME;
	}

}
?>