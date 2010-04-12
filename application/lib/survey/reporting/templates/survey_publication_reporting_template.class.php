<?php
require_once PATH :: get_reporting_path() . '/lib/reporting_template.class.php';
require_once dirname(__FILE__) . '/../blocks/survey_participant_reporting_block.class.php';
require_once dirname(__FILE__) . '/../blocks/survey_participant_mail_reporting_block.class.php';

class SurveyPublicationReportingTemplate extends ReportingTemplate
{
	function SurveyPublicationReportingTemplate($parent)
	{
		parent :: __construct($parent);
		$this->add_reporting_block(new SurveyParticipantReportingBlock($this));
		$this->add_reporting_block(new SurveyParticipantMailReportingBlock($this));		
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