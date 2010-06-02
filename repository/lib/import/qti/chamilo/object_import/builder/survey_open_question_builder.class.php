<?php

/**
 * Question builder for Survey Open Questions.
 * 
 * University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class SurveyOpenQuestionBuilder extends QuestionBuilder{
	
	static function factory($item, $source_root, $target_root, $category, $user, $log){
		if(	!class_exists('SurveyOpenQuestion') || 
			$item->has_templateDeclaration() ||
			self::has_score($item) ||
			count($item->list_interactions()) != 1){
			return null;
		}
		$main = self::get_main_interaction($item);
		if(!$main->is_extendedTextInteraction() && !$main->is_textEntryInteraction()){
			return null;
		}
		if(self::has_answers($item, $main)){
			return null;
		}
		
		return new self($source_root, $target_root, $category, $user, $log);
	}

	public function create_question(){
		$result = new SurveyOpenQuestion();
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
		return $result;
	}
}
