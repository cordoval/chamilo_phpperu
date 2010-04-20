<?php
require_once dirname ( __FILE__ ) . '/../survey_reporting_block.class.php';
require_once dirname ( __FILE__ ) . '/../../survey_manager/survey_manager.class.php';

class SurveyQuestionReportingBlock extends SurveyReportingBlock {
	
	const NO_ANSWER = 'noAnswer';
	const COUNT = 'count';
	
	private $question;
	private $option_matches;
	private $answer_count;
	
	public function count_data() {
		require_once (dirname ( __FILE__ ) . '/../../trackers/survey_question_answer_tracker.class.php');
		require_once (dirname ( __FILE__ ) . '/../../trackers/survey_participant_tracker.class.php');
		
		$question_id = $this->get_survey_question_id ();
		$this->question = RepositoryDataManager::get_instance ()->retrieve_content_object ( $question_id );
		$condition = new EqualityCondition ( SurveyQuestionAnswerTracker::PROPERTY_QUESTION_CID, $question_id );
		
		$tracker = new SurveyQuestionAnswerTracker ();
		$trackers = $tracker->retrieve_tracker_items_result_set ( $condition );
		
		$this->create_answer_count_matrix ();
		
		while ( $tracker = $trackers->next_result () ) {
			$this->add_answer_count ( $tracker->get_answer () );
		}
		return $this->create_reporting_data ();
	
	}
	
	public function retrieve_data() {
		return $this->count_data ();
	}
	
	function get_application() {
		return SurveyManager::APPLICATION_NAME;
	}
	
	private function create_reporting_data() {
		$reporting_data = new ReportingData ();
		$type = $this->question->get_type ();
		switch ($type) {
			case SurveyMatrixQuestion::get_type_name () :
				
				$matches = $this->get_question_matches ();
				$options = $this->get_question_options ();
				
				foreach ( $matches as $match ) {
					$reporting_data->add_row ( strip_tags ( $match ) );
				}
				
				$reporting_data->add_row ( self::NO_ANSWER );
				
				foreach ( $options as $option_key => $option ) {
					
					$reporting_data->add_category ( $option );
					
					foreach ( $matches as $match_key => $match ) {
						$reporting_data->add_data_category_row ( $option, strip_tags ( $match ), $this->answer_count [$option_key] [$match_key] );
					}
					$reporting_data->add_data_category_row ( $option, self::NO_ANSWER, $this->answer_count [$option_key] [self::NO_ANSWER] );
				
				}
				break;
			case SurveyMultipleChoiceQuestion::get_type_name () :
				$matches = $this->get_question_matches ();
				$options = $this->get_question_options ();
				
				foreach ( $matches as $match ) {
					$reporting_data->add_row ( strip_tags ( $match ) );
				}
				
//				$reporting_data->add_row ( self::NO_ANSWER );
				
				foreach ( $options as $option_key => $option ) {
					
					$reporting_data->add_category ( $option );
					
					foreach ( $matches as $match_key => $match ) {
						$reporting_data->add_data_category_row ( $option, strip_tags ( $match ), $this->answer_count [$option_key]);
					}
//					$reporting_data->add_data_category_row ( $option, self::NO_ANSWER, $this->answer_count [self::NO_ANSWER] );
				
				}
				break;
			default :
				;
				break;
		}
		
		return $reporting_data;
	}
	
	private function create_answer_count_matrix() {
		$type = $this->question->get_type ();
		switch ($type) {
			case SurveyMatrixQuestion::get_type_name () :
				$options = $this->get_question_options ();
				$matches = $this->get_question_matches ();
				
				$option_count = count ( $options ) - 1;
				
				while ( $option_count >= 0 ) {
					$match_count = count ( $matches ) - 1;
					while ( $match_count >= 0 ) {
						$this->answer_count [$option_count] [$match_count] = 0;
						$match_count --;
					}
					$this->answer_count [$option_count] [self::NO_ANSWER] = 0;
					$option_count --;
				}
				break;
			case SurveyMultipleChoiceQuestion::get_type_name () :
				
				$options = $this->get_question_options ();
				$option_count = count ( $options ) - 1;
				while ( $option_count >= 0 ) {
					$this->answer_count [$option_count] = 0;
					$option_count --;
				}
				$this->answer_count [self::NO_ANSWER] = 0;
				
				break;
			default :
				;
				break;
		}
	}
	
	private function get_question_options() {
		
		$options = array ();
		$type = $this->question->get_type ();
		switch ($type) {
			case SurveyMatrixQuestion::get_type_name () :
				$opts = $this->question->get_options ();
				foreach ( $opts as $option ) {
					$options [] = $option->get_value ();
				}
				break;
			case SurveyMultipleChoiceQuestion::get_type_name () :
				$opts = $this->question->get_options ();
				foreach ( $opts as $option ) {
					$options [] = $option->get_value ();
				}
				$options[] = self :: NO_ANSWER;
				break;
			default :
				;
				break;
		}
		return $options;
	}
	
	private function get_question_matches() {
		$matches = array ();
		$type = $this->question->get_type ();
		switch ($type) {
			case SurveyMatrixQuestion::get_type_name () :
				$matchs = $this->question->get_matches ();
				foreach ( $matchs as $match ) {
					$matches [] = $match;
				}
				break;
			case SurveyMultipleChoiceQuestion::get_type_name () :
				
				$matches [] = self::COUNT;
				
				break;
			default :
				;
				break;
		}
		//		$matches [] = TransLation::get ( self::NO_ANSWER );
		return $matches;
	}
	
	private function add_answer_count($answers) {
		
		dump ( $answers );
		//options are the keys of the answers array
		//the matches are the values of the $option arrays
		

		//check the keys against question options indexes that have a answer all others have no answer
		// register the chosen match with a +1 count;
		

		$type = $this->question->get_type ();
		switch ($type) {
			case SurveyMatrixQuestion::get_type_name () :
				$options_answered = array ();
				foreach ( $answers as $key => $option ) {
					$options_answered [] = $key;
					foreach ( $option as $match ) {
						$this->answer_count [$key] [$match] ++;
					}
				}
				$options = array ();
				foreach ( $this->answer_count as $key => $option ) {
					$options [] = $key;
				}
				$options_not_answered = array_diff ( $options, $options_answered );
				foreach ( $options_not_answered as $option ) {
					$this->answer_count [$option] [self::NO_ANSWER] ++;
				
				}
				break;
			case SurveyMultipleChoiceQuestion::get_type_name () :
				foreach ( $answers as $option ) {
					$options_answered [] = $option;
					$this->answer_count [$option] ++;
				}
				break;
			
			default :
				;
				break;
		}
		
		dump ( $this->answer_count );
		//for each option without answer add the no answer +1 count;
	

	}

}

?>