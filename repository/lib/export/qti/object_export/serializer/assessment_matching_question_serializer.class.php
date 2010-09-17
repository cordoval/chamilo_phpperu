<?php
require_once Path::get_repository_path(). 'lib/content_object/assessment_matching_question/assessment_matching_question_option.class.php';
 
/**
 * Serializer for matching questions.
 * 
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class AssessmentMatchingQuestionSerializer extends QuestionSerializer{
	
	static function factory($question, $target_root, $directory, $manifest, $toc){
		if($question instanceof AssessmentMatchingQuestion){
			return new self($target_root, $directory, $manifest, $toc);
		}else{
			return null;
		}
	}
	
	protected function has_answer_feedback($question){
		return true;
	}
	
    protected function get_question_score(AssessmentMatchingQuestion $question){
    	$result = 0;
        $answers = $question->get_options();
    	foreach($answers as $answer){
    		if($answer->get_score()>=0){
    			$result += $answer->get_score();
    		}
    	}
    	return $result;
    }

	protected function add_response_declaration(ImsQtiWriter $item, AssessmentMatchingQuestion $question){
		$id = Qti::RESPONSE;
		$cardinality =  Qti::CARDINALITY_MULTIPLE;
		$type = Qti::BASETYPE_DIRECTEDPAIR;
		$question_score = $this->get_question_score($question);
		$result = $item->add_responseDeclaration($id, $cardinality, $type);
		$correct_response = $result->add_correctResponse();
		$mapping = $result->add_mapping(0, $question_score, 0);
		
        $answers = $question->get_options();
		foreach($answers as $index => $answer){
			$question_id = "Q_$index";
			$answer_id = 'A_' . $answer->get_match();
			$key = "$question_id $answer_id";
			$score = $answer->get_score();
			$mapping->add_mapEntry($key, $score);
			$correct_response->add_value($key);
		}
		return $result;
	}
	
	protected function add_score_processing($response_processing, AssessmentMatchingQuestion $question){
		return $response_processing->add_standard_response_map_response();
	}

	protected function add_answer_feedback_processing(ImsQtiWriter $response_processing, $question){
		$response_processing->add_setOutcomeValue(Qti::FEEDBACK)->add_baseValue(Qti::BASETYPE_IDENTIFIER, Qti::FEEDBACK_SHOW);
	}
	
	protected function add_interaction(ImsQtiWriter $body, $question){
        $options = $question->get_options();
		$question_count = count($options);
				
		$label = 'display=matching';
		$result = $body->add_matchInteraction(ImsQtiWriter::RESPONSE, $question_count, true, '', '', '', $label);
		$questions = $result->add_simpleMatchSet();
		$answers = $result->add_simpleMatchSet();
		
		foreach($options as $index => $option){
			$question_id = "Q_$index";
			$text = $this->translate_text($option->get_value());
        	$feedback = $this->translate_text($option->get_feedback());
			$choice = $questions->add_simpleAssociableChoice($question_id, false, array(), 1)->add_flow($text);
			$choice->add_feedbackInline(Qti::FEEDBACK, Qti::FEEDBACK_SHOW, Qti::FEEDBACK_SHOW)->add_flow($feedback);
		}
		
		$matches = $question->get_matches();
		foreach($matches as $index => $match){
			$answer_id = "A_$index";
			$text = $this->translate_text($match);
			$answers->add_simpleAssociableChoice($answer_id, false, array(), $question_count)->add_flow($text);
		}
		return $result;
	}
}








?>