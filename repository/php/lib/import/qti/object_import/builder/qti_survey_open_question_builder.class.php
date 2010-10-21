<?php

/**
 * Question builder for Survey Open Questions.
 *
 * @copyright (c) 2010 University of Geneva
 * @author laurent.opprecht@unige.ch
 *
 */
class QtiSurveyOpenQuestionBuilder extends QtiQuestionBuilder{

	static function factory($item, $settings){
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

		return new self($settings);
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
