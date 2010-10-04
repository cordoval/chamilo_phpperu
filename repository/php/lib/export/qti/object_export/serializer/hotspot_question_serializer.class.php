<?php

/**
 * Serializer for hotspot questions. 
 * 
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class HotspotQuestionSerializer extends QuestionSerializer{
	
	public static function factory($question, $target_root, $directory, $manifest, $toc){
		if($question instanceof HotspotQuestion){
			return new self($target_root, $directory, $manifest, $toc);
		}else{
			return null;
		}
	}
	
	protected function has_answer_feedback($question){
		return true;
	}    
	
	protected function get_question_score(HotspotQuestion $question){
		//@todo: check the hotspot logic: multiple selection, ordered or not
		$multiple = true;
		if($multiple){
	    	$result = 0;
	        $answers = $question->get_answers();
	        foreach($answers as $answer){
	        	$result += $answer->get_weight();
	        }
		}else{
	    	$max = 0;
	        $answers = $question->get_answers();
	        foreach($answers as $answer){
	        	$max = max($max, $answer->get_weight());
	        }
	        $result = $max;
		}
    	return $result;
    }

	protected function add_response_declaration(ImsQtiWriter $item, HotspotQuestion $question){
        $answers = $question->get_answers();
        foreach($answers as $index => $answer){
        	$id = Qti::RESPONSE ."_$index";
        	$score = $answer->get_weight();
			$response = $item->add_responseDeclaration($id, Qti::CARDINALITY_SINGLE, Qti::BASETYPE_POINT);
    		$mapping = $response->add_areaMapping(0, $score, 0); 
		
			//@todo: move unserialize to get_hotspot_coordinates?
            $coordinates = unserialize($answer->get_hotspot_coordinates());
        	$coordinates = $this->serialize_coordinates($coordinates);
        	$mapping->add_areaMapEntry(Qti::SHAPE_POLY, $coordinates, $score);
        }
	}
	
	protected function add_score_processing(ImsQtiWriter $response_processing, $question){
        $answers = $question->get_answers();   
        foreach($answers as $index => $answer){
        	$response_id = Qti::RESPONSE ."_$index";
      		$condition = $response_processing->add_responseCondition();
      		$if = $condition->add_responseIf();
      		$if->add_isNull()->add_variable($response_id);
      		$sum = $if->add_setOutcomeValue(Qti::SCORE)->add_sum();
      		$sum->add_variable(Qti::SCORE);
      		$sum->add_default($response_id);
      		$sum = $condition->add_responseElse()->add_setOutcomeValue(Qti::SCORE)->add_sum();
      		$sum->add_variable(Qti::SCORE);
      		$sum->add_mapResponsePoint($response_id);
        }
	}

	protected function add_answer_feedback_declaration(ImsQtiWriter $item, $question){
        $answers = $question->get_answers();
		foreach($answers as $index=>$answer){
        	$id = Qti::FEEDBACK ."_$index";
			$result = $item->add_outcomeDeclaration_feedback($id);
			$result->add_defaultValue()->add_value('DEFAULT_FEEDBACK');
		}
	}
	
	protected function add_answer_feedback(ImsQtiWriter $item, $question){ 
        $answers = $question->get_answers();
		foreach($answers as $index=>$answer){
        	$id = Qti::FEEDBACK ."_$index";
        	$feedback = $this->translate_text($answer->get_comment());
			$item->add_modalFeedback($id, $id, 'show')->add_flow($feedback);
		}
	}

	protected function add_answer_feedback_processing(ImsQtiWriter $processing, $question){	
        $answers = $question->get_answers();
        foreach($answers as $index => $answer){
        	$response_id = Qti::RESPONSE ."_$index";
        	$feedback_id = Qti::FEEDBACK ."_$index";
    		$condition = $processing->add_responseCondition();
      		$if = $condition->add_responseIf();
      		$if->add_isNull()->add_variable($response_id);
      		$if->add_setOutcomeValue($feedback_id)->add_default($feedback_id);
            $coordinates = unserialize($answer->get_hotspot_coordinates());
        	$coordinates = $this->serialize_coordinates($coordinates);
        	$if = $condition->add_responseElseIf();
        	$if->add_inside(Qti::SHAPE_POLY, $coordinates)->add_variable($response_id);
        	$if->add_setOutcomeValue($feedback_id)->add_baseValue(Qti::BASETYPE_IDENTIFIER, $feedback_id);
        }
	}
	
	protected function add_interaction(ImsQtiWriter $body, HotspotQuestion $question){
		$result = $body->add_positionObjectStage();        
		$image = $question->get_image_object();
		$stage_local_path = Filesystem::create_safe_name($image->get_filename());
		$stage_local_path = 'resources/'.$stage_local_path;
		$result->add_object($stage_local_path, $image->get_mime_type());
		$this->register_resource($image->get_id(), $stage_local_path); 
		$interaction_image_full_path = Theme::get_common_image_system_path().'action_required.png';
		$interaction_image_local_path = 'resources/star.png';
		$this->register_resource($interaction_image_full_path, $interaction_image_local_path);
		
        $answers = $question->get_answers();
		foreach($answers as $index=>$answer){
        	$id = Qti::RESPONSE ."_$index";
			$interaction = $result->add_positionObjectInteraction($id, 1);
			$prompt = html_trim_tag($answer->get_answer(), 'p');
			$interaction->add_prompt($prompt);
			$interaction->add_object($interaction_image_local_path, 'image/png');
		}
		return $result;
	}
	
	protected function serialize_coordinates($points, $separator = ','){
		$head = reset($points);
		$points[] = $head;
		$result = array();
		foreach($points as $point){
			$result[] = $point[0];
			$result[] = $point[1];
		}
		return implode($separator, $result);
	}

}









?>