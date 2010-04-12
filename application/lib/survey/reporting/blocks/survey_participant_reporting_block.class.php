<?php
require_once dirname ( __FILE__ ) . '/../survey_reporting_block.class.php';
require_once dirname ( __FILE__ ) . '/../../survey_manager/survey_manager.class.php';
class SurveyParticipantReportingBlock extends SurveyReportingBlock {
	
	public function count_data() {
		
		require_once (dirname ( __FILE__ ) . '/../../trackers/survey_participant_tracker.class.php');
		
		$conditions = array ();
		
		$publication_id = $this->get_survey_publication_id ();
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
	
	public function retrieve_data() {
		return $this->count_data ();
	}
	
	function get_application() {
		return SurveyManager::APPLICATION_NAME;
	}
	
	public function get_available_displaymodes() {
		$modes = array ();
		$modes [ReportingFormatter::DISPLAY_TABLE] = Translation::get ( 'Table' );
		$modes [ReportingChartFormatter::DISPLAY_PIE] = Translation::get ( 'Chart:Pie' );
		$modes [ReportingChartFormatter::DISPLAY_BAR] = Translation::get ( 'Chart:Bar' );
		$modes [ReportingChartFormatter::DISPLAY_LINE] = Translation::get ( 'Chart:Line' );
		$modes [ReportingChartFormatter::DISPLAY_FILLED_CUBIC] = Translation::get ( 'Chart:FilledCubic' );
		return $modes;
	}
}
?>