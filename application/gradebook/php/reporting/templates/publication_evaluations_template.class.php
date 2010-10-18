<?php
require_once WebApplication :: get_application_class_path('gradebook') . 'reporting/blocks/publication_evaluations_reporting_block.class.php';

class PublicationEvaluationsTemplate extends ReportingTemplate
{	 
	function PublicationEvaluationsTemplate($parent)
	{
		parent :: __construct($parent);
		$this->add_reporting_block(new PublicationEvaluationsReportingBlock($this));
	}
	
	public function display_context()
	{
  
	}
	
	function get_application()
	{
		return GradebookManager::APPLICATION_NAME;
	}
} 
?>