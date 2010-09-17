<?php

class AssessmentOpenQuestionBuilder extends QuestionBuilder{
	
	static function factory($item, $settings){
		if(	!class_exists('AssessmentOpenQuestion') || 
			$item->has_templateDeclaration() ||
			!self::has_score($item)){
			return null;
		}
		$interactions = $item->list_interactions();
		$upload_count = 0;
		$text_count = 0;
		foreach($interactions as $interaction){
			if($interaction->is_extendedTextInteraction()){
				$text_count++;
			}else if($interaction->is_uploadInteraction()){
				$upload_count++;
			}else{
				return null;
			}
			if(self::has_answers($item, $interaction)){
				return null;
			}
		}
		if($text_count>1 || $upload_count>1 || ($text_count+$upload_count)==0){
			return null;
		}
		return new self($settings);
	}

	public function create_question(){
		$result = new AssessmentOpenQuestion();
        return $result;
	}
	
	/**
	 * 
	 * @param ImsXmlReader $item
	 */
	public function build($item){
		$result = $this->create_question();
        $result->set_title($item->get_title());
        $result->set_description($this->get_question_text($item));
        $result->set_question_type($this->get_question_type($item));
		return $result;
	}
	
	protected function get_question_type($item){
		$has_upload = $item->has_uploadInteraction();
		$has_text = $item->has_extendedTextInteraction();
		if($has_text && $has_upload){
			return AssessmentOpenQuestion::TYPE_OPEN_WITH_DOCUMENT;
		}else if($has_text && !$has_upload){
			return AssessmentOpenQuestion::TYPE_OPEN;
		}else if(!$has_text && $has_upload){
			return AssessmentOpenQuestion::TYPE_DOCUMENT;
		}else{
			throw new Exception('Case not supported');
		}
	}
}
