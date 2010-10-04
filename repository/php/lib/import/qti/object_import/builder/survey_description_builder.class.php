<?php

class SurveyDescriptionBuilder extends QuestionBuilder{

	static function factory($item, $settings){
		if(	!class_exists('SurveyDescription') ||
			$item->has_templateDeclaration() ||
			self::has_score($item) ||
			count($item->list_interactions())>0){
				return null;
		}
		return new self($settings);
	}

	public function create_question(){
		$result = new SurveyDescription();
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





