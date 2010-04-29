<?php
require_once dirname (__FILE__) . '/../evaluations_reporting_block.class.php';
require_once dirname (__FILE__) . '/../../gradebook_manager/gradebook_manager.class.php';
require_once dirname(__FILE__) . '/../../evaluation_format/evaluation_format.class.php';


class PublicationEvaluationsReportingBlock extends EvaluationsReportingBlock
{
	public function count_data()
	{	
		$reporting_data = new ReportingData();
		$reporting_data->set_rows(array(Translation :: get('EvaluationDate'),Translation :: get('User'), Translation :: get('Evaluator'), Translation :: get('Score'), Translation :: get('Comment')));
		$application = Request :: get(GradebookManager :: PARAM_PUBLICATION_TYPE);
		$publication_id = Request :: get(GradebookManager :: PARAM_PUBLICATION_ID);
		$data = GradebookManager :: retrieve_all_evaluations_on_publication($application, $publication_id);
		if($a = $data->next_result())
		{
			while($evaluation = $data->next_result())
			{
				$optional_properties = $evaluation->get_optional_properties();
				$format = GradebookManager :: retrieve_evaluation_format($evaluation->get_format_id());
				$evaluation_format = EvaluationFormat :: factory($format->get_title());
				$evaluation_format->set_score($optional_properties['score']);
				
				$reporting_data->add_category($evaluation->get_id());
	            $reporting_data->add_data_category_row($evaluation->get_id(), Translation :: get('EvaluationDate'), $evaluation->get_evaluation_date());
	            $reporting_data->add_data_category_row($evaluation->get_id(), Translation :: get('User'), $optional_properties['user']);
				$reporting_data->add_data_category_row($evaluation->get_id(), Translation :: get('Evaluator'), $optional_properties['evaluator']);
				$reporting_data->add_data_category_row($evaluation->get_id(), Translation :: get('Score'), $evaluation_format->get_formatted_score());
				$reporting_data->add_data_category_row($evaluation->get_id(), Translation :: get('Comment'), $optional_properties['comment']);
				$reporting_data->hide_categories();
			}
		}
		else
		{
			$application_manager = WebApplication :: factory($application);
        	$content_object_publication = $application_manager->get_content_object_publication_attribute($publication_id);
//			$gdm = GradebookDataManager :: get_instance();
//			$internal_items = $gdm->retrieve_internal_item_by_publication($application, $publication_id);
			$udm = UserDataManager :: get_instance();
			$connector = GradeBookConnector :: factory($application);
			$user = $connector->get_tracker_user($publication_id);
			$date = $connector->get_tracker_date($publication_id);
			$score = $connector->get_tracker_score($publication_id);
			$publisher = $udm->retrieve_user($content_object_publication->get_publisher_user_id())->get_fullname();
			for($i=0;$i<count($user);$i++)
			{
				$username = $udm->retrieve_user($user[$i])->get_fullname();
				$reporting_data->add_category($date[$i]);
	            $reporting_data->add_data_category_row($date[$i], Translation :: get('EvaluationDate'), $date[$i]);
	            $reporting_data->add_data_category_row($date[$i], Translation :: get('User'), $username);
				$reporting_data->add_data_category_row($date[$i], Translation :: get('Evaluator'), $publisher);
				$reporting_data->add_data_category_row($date[$i], Translation :: get('Score'), $score[$i] . '%');
				$reporting_data->add_data_category_row($date[$i], Translation :: get('Comment'), 'automatic generated result');
				$reporting_data->hide_categories();
			}
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