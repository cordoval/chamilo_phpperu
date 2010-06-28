<?php
require_once dirname ( __FILE__ ) . '/../internship_organizer_reporting_block.class.php';
require_once dirname ( __FILE__ ) . '/../../internship_organizer_manager/internship_organizer_manager.class.php';
class PeriodUserReportingBlock extends InternshipOrganizerReportingBlock {
	
	public function count_data() {
		
//		require_once (dirname ( __FILE__ ) . '/../../trackers/survey_participant_mail_tracker.class.php');
//		
//		$conditions = array ();
//		
//		$publication_id = $this->get_survey_publication_id ();
//		$condition = new EqualityCondition ( SurveyParticipantMailTracker::PROPERTY_SURVEY_PUBLICATION_ID, $publication_id );
//		
//		$tracker = new SurveyParticipantMailTracker ();
//		$trackers = $tracker->retrieve_tracker_items_result_set ( $condition );
//		
//		$mails [Translation::get ( SurveyParticipantMailTracker::STATUS_MAIL_NOT_SEND )] = 0;
//		$mails [Translation::get ( SurveyParticipantMailTracker::STATUS_MAIL_SEND )] = 0;
//		
//		while ( $tracker = $trackers->next_result () ) {
//			$status = $tracker->get_status ();
//			switch ($status) {
//				case SurveyParticipantMailTracker::STATUS_MAIL_NOT_SEND :
//					$mails [Translation::get ( SurveyParticipantMailTracker::STATUS_MAIL_NOT_SEND )] ++;
//					break;
//				case SurveyParticipantMailTracker::STATUS_MAIL_SEND :
//					$mails [Translation::get ( SurveyParticipantMailTracker::STATUS_MAIL_SEND )] ++;
//					break;
//							
//			}
//		}
//		$reporting_data = new ReportingData();
//		
//		$reporting_data->set_categories ( array (Translation::get (  'MailNotSend' ), Translation::get ( 'MailSend' )) );
//		$reporting_data->set_rows ( array (Translation::get ( 'Count' ) ) );
//		
//		$reporting_data->add_data_category_row (Translation::get ( 'MailNotSend' ), Translation::get ( 'Count' ), $mails [Translation::get (  SurveyParticipantMailTracker::STATUS_MAIL_NOT_SEND )] );
//		$reporting_data->add_data_category_row (Translation::get ( 'MailSend' ), Translation::get ( 'Count' ), $mails [Translation::get (  SurveyParticipantMailTracker::STATUS_MAIL_SEND )] );
//		
//		return $reporting_data;
	}
	
	public function retrieve_data() {
		return $this->count_data ();
	}
	
	function get_application() {
		return InternshipOrganizerManager::APPLICATION_NAME;
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