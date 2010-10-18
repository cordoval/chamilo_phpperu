<?php

/**
 * $Id: survey_question_type_reporting.class.php $Shoira Mukhsinova
 * @package application/lib/survey/reporting/blocks
 */
require_once dirname ( __FILE__ ) . '/../survey_reporting_block.class.php';
require_once dirname ( __FILE__ ) . '/../../survey_manager/survey_manager.class.php';


class SurveyQuestionTypeReportingBlock extends SurveyReportingBlock {
	
	const MATRIX_TYPE_RADIO = " Matrix Type Radio";
	const MATRIX_TYPE_CHECKBOX =" Matrix Type Checkbox";
	const ANSWER_TYPE_RADIO = " Answer Type Radio";
	const ANSWER_TYPE_CHECKBOX= " Answer Type Checkbox";
	const SINGLE_SELECT_TYPE = " Single Select Type";
	const MULTIPLE_SELECT_TYPE = " Multiple Select Type";
	
	public function count_data() {
		
		require_once (dirname ( __FILE__ ) . '/../../trackers/survey_participant_tracker.class.php');
		
		
		$publication_id = $this->get_survey_publication_id ();
		
		$survey_publication = SurveyDataManager::get_instance()->retrieve_survey_publication($publication_id);
		$survey = $survey_publication->get_publication_object();
		
		$question_type = array();
		
		
        $question_type [Translation::get ( SurveyRatingQuestion :: get_type_name())] = 0;
        $question_type [Translation::get ( SurveyOpenQuestion :: get_type_name())] = 0;
        $question_type [Translation::get ( SurveyMultipleChoiceQuestion :: get_type_name())] = 0;
        $question_type [Translation::get ( SurveyMultipleChoiceQuestion :: get_type_name()).self :: ANSWER_TYPE_CHECKBOX] = 0;
        $question_type [Translation::get ( SurveyMultipleChoiceQuestion :: get_type_name()). self:: ANSWER_TYPE_RADIO] = 0;
        $question_type [Translation::get ( SurveyMatchingQuestion :: get_type_name())] = 0;
        $question_type [Translation::get ( SurveySelectQuestion :: get_type_name())] = 0;
        $question_type [Translation::get ( SurveySelectQuestion :: get_type_name()). self::SINGLE_SELECT_TYPE] = 0;
        $question_type [Translation::get ( SurveySelectQuestion :: get_type_name()). self::MULTIPLE_SELECT_TYPE] = 0;
        $question_type [Translation::get ( SurveyMatrixQuestion :: get_type_name())] = 0;
        $question_type [Translation::get (SurveyMatrixQuestion :: get_type_name()). self:: MATRIX_TYPE_RADIO] = 0;
        $question_type [Translation::get (SurveyMatrixQuestion :: get_type_name()). self:: MATRIX_TYPE_CHECKBOX] = 0;
        $question_type [Translation::get ( SurveyDescription :: get_type_name())] = 0;
        
		
		
		foreach ($survey->get_pages(false) as $page){
			foreach ($page->get_questions(false) as $question){
				
				switch ($question->get_type_name()){
					case SurveyMatchingQuestion::get_type_name():
					 	$question_type [Translation::get ( SurveyRatingQuestion :: get_type_name())] ++;
						break;
					case SurveyOpenQuestion :: get_type_name():
						$question_type [Translation::get ( SurveyOpenQuestion :: get_type_name())] ++;
						break;
					case  SurveyMultipleChoiceQuestion :: get_type_name(): 
						$question_type [Translation::get ( SurveyMultipleChoiceQuestion :: get_type_name())] ++;
						switch ($question->get_answer_type()){
					 	 	case MultipleChoiceQuestion :: ANSWER_TYPE_RADIO:
					 	 		$question_type [Translation::get ( SurveyMultipleChoiceQuestion :: get_type_name()). self :: ANSWER_TYPE_CHECKBOX] ++;
					 	 		break;
					 	 	case MultipleChoiceQuestion :: ANSWER_TYPE_CHECKBOX:
					 	 		$question_type [Translation::get ( SurveyMultipleChoiceQuestion :: get_type_name()). self::ANSWER_TYPE_RADIO] ++;
					 	 		break;
					 	 }
						break;
					case SurveyMatchingQuestion :: get_type_name():
						$question_type [Translation::get ( SurveyMatchingQuestion :: get_type_name())] ++;
						break;
					case SurveySelectQuestion :: get_type_name():
						$question_type [Translation::get ( SurveySelectQuestion :: get_type_name())] ++;
						
						switch ($question->get_answer_type()){
					 	 	case 0:
					 	 		$question_type [Translation::get ( SurveySelectQuestion :: get_type_name()). self::SINGLE_SELECT_TYPE] ++;
					 	 		break;
					 	 	case 1:
					 	 		$question_type [Translation::get ( SurveySelectQuestion :: get_type_name()). self::MULTIPLE_SELECT_TYPE] ++;
					 	 		break;
					 	 }
						break;
					case SurveyMatrixQuestion :: get_type_name():
					 	 $question_type [Translation::get ( SurveyMatrixQuestion :: get_type_name())] ++; 
					 	 switch ($question->get_matrix_type()){
					 	 	case MatrixQuestion :: MATRIX_TYPE_RADIO:
					 	 		$question_type [Translation::get (SurveyMatrixQuestion :: get_type_name()). self:: MATRIX_TYPE_RADIO] ++;
					 	 		break;
					 	 	case MatrixQuestion :: MATRIX_TYPE_CHECKBOX:
					 	 		$question_type [Translation::get (SurveyMatrixQuestion :: get_type_name()). self:: MATRIX_TYPE_CHECKBOX] ++;
					 	 		break;
					 	 }
					 	 break;
					/*case SurveyDescription :: get_type_name():
						 $question_type [SurveyDescription :: get_type_name()] ++;
						 break;
					*/
				}
			}
		}
		
		$reporting_data = new ReportingData();
		
		$reporting_data->set_categories ( array (Translation::get ( SurveyRatingQuestion :: get_type_name()),
												Translation::get ( SurveyOpenQuestion :: get_type_name()),
											//	Translation::get ( SurveyMultipleChoiceQuestion :: get_type_name()),
												Translation::get ( SurveyMultipleChoiceQuestion :: get_type_name()).self :: ANSWER_TYPE_CHECKBOX,
												Translation::get ( SurveyMultipleChoiceQuestion :: get_type_name()).self :: ANSWER_TYPE_RADIO,
												Translation::get ( SurveyMatchingQuestion :: get_type_name()),
												Translation::get ( SurveySelectQuestion :: get_type_name()).self::SINGLE_SELECT_TYPE,
												Translation::get ( SurveySelectQuestion :: get_type_name()).self::MULTIPLE_SELECT_TYPE,
												//Translation::get ( SurveyMatrixQuestion :: get_type_name()),
												Translation::get (SurveyMatrixQuestion :: get_type_name()). self:: MATRIX_TYPE_RADIO,
												Translation::get (SurveyMatrixQuestion :: get_type_name()). self:: MATRIX_TYPE_CHECKBOX
												 ) );
												 
		$reporting_data->set_rows ( array (Translation::get ( 'Count' ) ) );
		
		$reporting_data->add_data_category_row ( Translation::get (  SurveyRatingQuestion :: get_type_name()), Translation::get ( 'Count' ), $question_type [Translation::get ( SurveyRatingQuestion :: get_type_name())] );
		//$reporting_data->add_data_category_row ( Translation::get (  SurveyOpenQuestion :: get_type_name()), Translation::get ( 'Count' ), $question_type [Translation::get( SurveyOpenQuestion :: get_type_name())] );
		$reporting_data->add_data_category_row ( Translation::get (  SurveyMultipleChoiceQuestion :: get_type_name()), Translation::get ( 'Count' ), $question_type [Translation::get( SurveyMultipleChoiceQuestion :: get_type_name())] );
		$reporting_data->add_data_category_row ( Translation::get (  SurveyMultipleChoiceQuestion :: get_type_name()).self::ANSWER_TYPE_CHECKBOX, Translation::get ( 'Count' ), $question_type [Translation::get( SurveyMultipleChoiceQuestion :: get_type_name()).self::ANSWER_TYPE_CHECKBOX] );
		$reporting_data->add_data_category_row ( Translation::get (  SurveyMultipleChoiceQuestion :: get_type_name()).self::ANSWER_TYPE_RADIO, Translation::get ( 'Count' ), $question_type [Translation::get( SurveyMultipleChoiceQuestion :: get_type_name()).self::ANSWER_TYPE_RADIO] );
		$reporting_data->add_data_category_row ( Translation::get (  SurveyMatchingQuestion :: get_type_name()), Translation::get ( 'Count' ), $question_type [Translation::get( SurveyMatchingQuestion :: get_type_name())] );
		$reporting_data->add_data_category_row ( Translation::get (  SurveySelectQuestion :: get_type_name()).self::SINGLE_SELECT_TYPE, Translation::get ( 'Count' ), $question_type [Translation::get( SurveySelectQuestion :: get_type_name()).self::SINGLE_SELECT_TYPE] );
		$reporting_data->add_data_category_row ( Translation::get (  SurveySelectQuestion :: get_type_name()).self ::MULTIPLE_SELECT_TYPE, Translation::get ( 'Count' ), $question_type [Translation::get( SurveySelectQuestion :: get_type_name()).self ::MULTIPLE_SELECT_TYPE] );
		//$reporting_data->add_data_category_row ( Translation::get (  SurveyMatrixQuestion :: get_type_name()), Translation::get ( 'Count' ), $question_type [Translation::get( SurveyMatrixQuestion :: get_type_name())] );
		$reporting_data->add_data_category_row ( Translation::get (  SurveyMatrixQuestion :: get_type_name()).self ::MATRIX_TYPE_CHECKBOX, Translation::get ( 'Count' ), $question_type [Translation::get( SurveyMatrixQuestion :: get_type_name()).self ::MATRIX_TYPE_CHECKBOX] );
		$reporting_data->add_data_category_row ( Translation::get (  SurveyMatrixQuestion :: get_type_name()).self ::MATRIX_TYPE_RADIO, Translation::get ( 'Count' ), $question_type [Translation::get( SurveyMatrixQuestion :: get_type_name()).self ::MATRIX_TYPE_RADIO] );
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