<?php

require_once_all(dirname(__FILE__) .'/builder/*.class.php');

/**
 * Base class for questions' builders. 
 * Builders are responsible to construct a chamilo question object.
 * Relies on the import strategies to extract values from the QTI file and on the QTI renderer
 * to render the question's parts. 
 * 
 * University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class QuestionBuilder extends BuilderBase{

	public static function is_calculated(ImsXmlReader $item){
		if(!$item->has_templateDeclaration()){
			return false;
		}
		$templates = $item->list_templateDeclaration();
		foreach($templates as $template){
			$base_type = $template->baseType;
			if($base_type != Qti::BASETYPE_FLOAT && $base_type != Qti::BASETYPE_INTEGER){
				return false;
			}
		}
		return true;
	} 
	
	/**
	 * 
	 * @param ImsQtiReader $item
	 * @return ImsQtiReader
	 */
	static function get_main_interaction($item){
		return QtiImportStrategyBase::get_main_interaction($item);
	}

	static function has_answers($item, $interaction){
		return QtiImportStrategyBase::has_answers($item, $interaction);
	}
	
	static function is_numeric_interaction($item, $interaction){
		return QtiImportStrategyBase::is_numeric_interaction($item, $interaction);
	}

	static function has_label($item, $interaction, $key, $value){
		return QtiImportStrategyBase::has_label($item, $interaction, $key, $value);
	}
		
	public function __construct($source_root, $target_root, $category, $user, $log){
		parent::__construct($source_root, $target_root, $category, $user, null, $log); 
	}
	
	protected function get_question_text($item){
		$result = $this->get_strategy()->get_question_text($item);
		$result = $this->translate_images($result);
		return $result;
	}
	
	protected function create_question(){
        return null;
	}
}











