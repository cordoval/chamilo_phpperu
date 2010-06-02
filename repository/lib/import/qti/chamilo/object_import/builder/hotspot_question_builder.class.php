<?php

/**
 * Question builder for hotspot questions.
 * 
 * University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class HotspotQuestionBuilder extends QuestionBuilder{
	
	static function factory($item, $source_root, $target_root, $category, $user, $log){
		if(	!class_exists('HotspotQuestion') || 
			$item->has_templateDeclaration() ||
			!self::has_score($item)){
			return null;
		}
		if(count($item->all_positionObjectStage())!=1){
			return null;
		}
		$interactions = $item->list_interactions();
		foreach($interactions as $interaction){
			if(	!$interaction->is_positionObjectInteraction() ||
				!self::has_answers($item, $interaction)){
				return null;
			}
		}
		return new self($source_root, $target_root, $category, $user, $log);
	}
	
	public function create_question(){
		$result = new HotspotQuestion();
        return $result;
	}
		
	public function build(ImsXmlReader $item){
		$result = $this->create_question();
        $result->set_title($item->get_title());
        $result->set_description($this->get_question_text($item));
		
        $stage = reset($item->all_positionObjectStage());
    	$image_path = $this->get_source_root() . $stage->get_object()->data;
    	$doc = $this->create_document($image_path);
        $result->set_image($doc->get_id());
        //$result->attach_content_object($doc->get_id());
                
		$interactions = $item->list_interactions();
    	foreach($interactions as $interaction){
    		$responses = $this->get_possible_responses($item, $interaction);
    		foreach($responses as $response){
	    		if(	$response instanceof ImsQtiReader && $response->shape == Qti::SHAPE_POLY){
		    		$coords = $response->coords;
		    		$coords = shape::string_to_polygone($coords);
		    		$answer = shape::get_point_inside_polygon($coords);
		    		$coords = serialize($coords);
		    		$label = $this->to_html($interaction->get_prompt());
			    	$value = $this->get_answer($answer);
			    	$score = $this->get_score($item, $interaction, $answer);
			    	$feedback = $this->get_feedback($item, $interaction, $answer);
			    	$option = new HotspotQuestionAnswer($label, $feedback, $score, $coords);
			    	$result->add_answer($option);
			    	break; //chamilo supports only one match per transaction
	    		}
    		}
    	}
		return $result;
	}
	
}








