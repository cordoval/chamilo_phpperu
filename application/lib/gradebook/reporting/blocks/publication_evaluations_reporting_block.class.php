<?php
require_once dirname (__FILE__) . '/../evaluations_reporting_block.class.php';
require_once dirname (__FILE__) . '/../../gradebook_manager/gradebook_manager.class.php';

class PublicationEvaluationsReportingBlock extends EvaluationsReportingBlock
{
	public function count_data()
	{	
		$reporting_data = new ReportingData();
		
		$reporting_data->set_categories(Translation :: get('EvluationDate'), Translation :: get('User'), Translation :: get('Evaluator'), Translation :: get('Score'),Translation :: get('Comment'));
		$reporting_data->set_rows(Translation :: get('EvluationDate'), Translation :: get('User'), Translation :: get('Evaluator'), Translation :: get('Score'),Translation :: get('Comment'));
		$reporting_data->add_data_category_row('test1', 'aantal', 1);
		$reporting_data->add_data_category_row('test2', 'aantal', 2);
		$reporting_data->add_data_category_row('test3', 'aantal', 3);
		
		return $reporting_data;
		/*********************************************************/
		$conditions = array ();
		
		$condition = new EqualityCondition ( SurveyParticipantTracker::PROPERTY_SURVEY_PUBLICATION_ID, $publication_id );
		
		$tracker = new SurveyParticipantTracker ();
		$trackers = $tracker->retrieve_tracker_items_result_set ( $condition );
		
		$users [Translation::get ( SurveyParticipantTracker::STATUS_FINISHED )] = 0;
		$users [Translation::get ( SurveyParticipantTracker::STATUS_NOTSTARTED )] = 0;
		$users [Translation::get ( SurveyParticipantTracker::STATUS_STARTED )] = 0;
		
		while ( $tracker = $trackers->next_result () ) {
			$status = $tracker->get_status ();
			switch ($status) {
				case SurveyParticipantTracker::STATUS_FINISHED :
					$users [Translation::get ( SurveyParticipantTracker::STATUS_FINISHED )] ++;
					break;
				case SurveyParticipantTracker::STATUS_NOTSTARTED :
					$users [Translation::get ( SurveyParticipantTracker::STATUS_NOTSTARTED )] ++;
					break;
				case SurveyParticipantTracker::STATUS_STARTED :
					$users [Translation::get ( SurveyParticipantTracker::STATUS_STARTED )] ++;
					break;
			
			}
		}
		$reporting_data = new ReportingData();
		
		$reporting_data->set_categories ( array (Translation::get (  SurveyParticipantTracker::STATUS_FINISHED ), Translation::get ( SurveyParticipantTracker::STATUS_NOTSTARTED ) , Translation::get ( SurveyParticipantTracker::STATUS_STARTED )) );
		$reporting_data->set_rows ( array (Translation::get ( 'Count' ) ) );
		
		$reporting_data->add_data_category_row (Translation::get (  SurveyParticipantTracker::STATUS_FINISHED ), Translation::get ( 'Count' ), $users [Translation::get (  SurveyParticipantTracker::STATUS_FINISHED )] );
		$reporting_data->add_data_category_row (Translation::get (  SurveyParticipantTracker::STATUS_NOTSTARTED ), Translation::get ( 'Count' ), $users [Translation::get (  SurveyParticipantTracker::STATUS_NOTSTARTED )] );
		$reporting_data->add_data_category_row (Translation::get (  SurveyParticipantTracker::STATUS_STARTED ), Translation::get ( 'Count' ), $users [Translation::get (  SurveyParticipantTracker::STATUS_STARTED )] );
		
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