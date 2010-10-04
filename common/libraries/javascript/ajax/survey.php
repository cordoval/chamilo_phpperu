<?php
require_once dirname ( __FILE__ ) . '/../../global.inc.php';
require_once Path::get_application_path () . 'lib/survey/survey_data_manager.class.php';

function process_question_results($question_results) {
	$question_selections = array ();
	
	foreach ( $question_results as $question_identifier => $question_value ) {
		$question_identifier = explode ( '_', $question_identifier );
		$question_type = $question_identifier [0];
		$question_id = $question_identifier [1];
		$question_name = 'survey_question_' . $question_id;
		
		$contains_matches = (count ( array_slice ( $question_identifier, 2, - 1 ) ) > 1);
		
		if ($question_type == 'radio') {
			if ($contains_matches) {
				$question_selections [$question_name] [$question_identifier [2]] = $question_value;
			} else {
				$question_selections [$question_name] = $question_value;
			}
		} elseif ($question_type == 'checkbox') {
			if ($contains_matches) {
				$question_selections [$question_name] [$question_identifier [2]] [] = $question_identifier [3];
			} else {
				$question_selections [$question_name] [] = $question_identifier [2];
			}
		}
	}
	
	return $question_selections;
}

$survey_publication_id = Request::post ( 'survey_publication' );
$survey_publication = SurveyDataManager::get_instance ()->retrieve_survey_publication ( $survey_publication_id );

$question_results = Request::post ( 'results' );
$question_results = str_replace ( '\"', '"', $question_results );
$question_results = json_decode ( $question_results, true );

if (count ( $question_results ) > 0) {
	
	$result;
	foreach ( $question_results as $key => $value ) {
		$result = $key;
	}
	
	$question_identifier = explode ( '_', $result );
	
	$page_index = end ( $question_identifier );
	
	$question_selections = process_question_results ( $question_results );
	
	/**
	 * Verification of question visiblity goes here.
	 *
	 * Expected format for $question_visibility:
	 * A single dimension array containing the question ids
	 * (survey_question_x) as keys and a boolean as a value
	 *
	 * The example below just loops through all questions
	 * with selected answers and hides them
	 */
	
	$survey_page = RepositoryDataManager::get_instance ()->retrieve_content_object ( $page_index );
	//    dump($page_index);
	//    dump($question_results);
	

	$question_visibility = array ();
	$complex_question_items = $survey_page->get_questions ( true );
	while ( $complex_question_item = $complex_question_items->next_result () ) {
		$question_id = $complex_question_item->get_ref ();
		$id = 'survey_question_' . $question_id;
		
		if ($complex_question_item->get_visible () == 1) {
			$question_visibility [$id] = true;
		} else {
			$question_visibility [$id] = false;
		}
	}
	//    dump($question_visibility);
	$configs = $survey_page->get_config ();
	//    
	//    $question_visibility = array();
	$rconfig;
	$resultq;
	$ranswer;
	
//	dump($configs);
	
	foreach ( $question_selections as $question_id => $question_result ) {
		
		$resultq = $ids = explode ( '_', $question_id );
		$sqi = $ids [2];
		
		foreach ( $configs as $config ) {
			$rconfig = $config;
			$from_question_id = $config [SurveyPage::FROM_VISIBLE_QUESTION_ID];
			if ($sqi == $from_question_id) {
				$answer = $config [SurveyPage::ANSWERMATCHES];
//				dump($answer);
				$answers_to_match = array ();
				foreach ( $answer as $key => $value ) {
					$oids = explode ( '_', $key );
					//					dump ( $oids );
					if (count ( $oids ) == 3) {
						$answers_to_match [] = $oids [1];
					}elseif (count($oids) == 4){
//						dump($oids);
						$option = $oids[1];
						$answers_to_match [$option] = $value;
//						$answers_to_match [$option] = $oids [2];
						
					}
				}
//				dump ( $answers_to_match );
				//				
//				dump ( $question_result );
				
				if (! empty ( $question_result )) {
					if (! is_array ( $question_result )) {
						$question_result = array ($question_result );
					}
				}
				
//				dump ( $question_result );
				
				//				foreach ( $question_result as $key => $value ) {
				//					dump ( $key );
				//					dump($value);
				//				}
				

				$diff = array_diff ( $question_result, $answers_to_match );
				if (count ( $diff ) == 0) {
					foreach ( $config [SurveyPage::TO_VISIBLE_QUESTIONS_IDS] as $id ) {
						$qid = 'survey_question_' . $id;
						$question_visibility [$qid] = true;
					}
				}
			}
		
		}
		
	//	    	$question_visibility['survey_question_62'] = true;
	}
	//	
	//	    dump($question_visibility);
	//    dump($resultq);
	//    dump($rconfig);
	

	//    
	echo json_encode ( $question_visibility );
} else {
	echo json_encode ( array () );
}
?>