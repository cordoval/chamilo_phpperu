<?php
require_once dirname (__FILE__) . '/../evaluations_reporting_block.class.php';
require_once dirname (__FILE__) . '/../../gradebook_manager/gradebook_manager.class.php';
require_once dirname(__FILE__) . '/../../evaluation_format/evaluation_format.class.php';

class PublicationEvaluationsReportingBlock extends EvaluationsReportingBlock
{
	public function count_data()
	{	
		$reporting_data = new ReportingData();
		$reporting_data->set_rows(array(Translation :: get('User'), Translation :: get('Evaluator'), Translation :: get('Score'), Translation :: get('Comment')));
		$data = GradebookManager :: retrieve_all_evaluations_on_publication(Request :: get(GradebookManager :: PARAM_PUBLICATION_TYPE),Request :: get(GradebookManager :: PARAM_PUBLICATION_ID));
		
		while ($evaluation = $data->next_result())
		{
			$optional_properties = $evaluation->get_optional_properties();
			$format = GradebookManager :: retrieve_evaluation_format($evaluation->get_format_id());
			$evaluation_format = EvaluationFormat :: factory($format->get_title());
			$evaluation_format->set_score($optional_properties['score']);
			
			$reporting_data->add_category($evaluation->get_evaluation_date());
            $reporting_data->add_data_category_row($evaluation->get_evaluation_date(), Translation :: get('User'), $optional_properties['user']);
			$reporting_data->add_data_category_row($evaluation->get_evaluation_date(), Translation :: get('Evaluator'), $optional_properties['evaluator']);
			$reporting_data->add_data_category_row($evaluation->get_evaluation_date(), Translation :: get('Score'), $evaluation_format->get_formatted_score());
			$reporting_data->add_data_category_row($evaluation->get_evaluation_date(), Translation :: get('Comment'), $optional_properties['comment']);
		}
		return $reporting_data;
	}	
	
	public function retrieve_data()
	{
		return $this->count_data();
	}
	
	function get_application()
	{
		return GradebookManager::APPLICATION_NAME;
	}
}
?>