<?php

/**
 * Question builder for Fillinblansk questions.
 * 
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class FillInBlanksQuestionBuilder extends QuestionBuilder{
	
	static function factory($item, $settings){
		if(	!class_exists('FillInBlanksQuestion') ||
			count($item->list_interactions())<2 || 
			$item->has_templateDeclaration() ||
			!self::has_score($item)){
			return null;
		}
		$interactions = $item->list_interactions();
		foreach($interactions as $interaction){
			if(! self::accept_interaction($interaction)){
				return null;
			}
		}
		
		return new self($settings);
	}
	
	static function accept_interaction($interaction){
		return 	$interaction->is_extendedTextInteraction() ||
				$interaction->is_textEntryInteraction() ||
				$interaction->is_inlineChoiceInteraction() ||
				$interaction->is_choiceInteraction();
	}
	
	public function create_question(){
		$result = new FillInBlanksQuestion();
        return $result;
	}
	
	protected function get_question_type($item){
		$interactions = $item->list_interactions();
		foreach($interactions as $interaction){
			if($interaction->is_extendedTextInteraction() ||
				$interaction->is_textEntryInteraction()){
				return FillInBlanksQuestion::TYPE_TEXT;
			}
		}
		return FillInBlanksQuestion::TYPE_SELECT;
	}
	
	public function build(ImsXmlReader $item){
		$result = $this->create_question();
        $result->set_title($item->get_title());
        $result->set_answer_text($this->get_answer_text($item));
        $result->set_question_type($this->get_question_type($item));
		return $result;
	}
	
	protected function get_answer_text($item){
		$renderer = new FillInBlanksQuestionRenderer($this->get_strategy(), $item);
		$result = $renderer->to_text($item);
		return $result; 
	}
	
}

/**
 * Render a QTI assessmentItem into chamilo's FillInBlanksQuestion/Cloze format.
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class FillInBlanksQuestionRenderer extends QtiRendererBase{
	
	/**
	 * 
	 * @var QtiImportStrategy
	 */
	private $strategy = null;
	private $assessment = null;
	
	public function __construct(QtiImportStrategyBase $strategy, $assessment){
		parent::__construct($strategy->get_renderer()->get_resource_manager());
		$this->strategy = $strategy;
		$this->assessment = $assessment;
	}
	
	protected function get_strategy(){
		return $this->strategy;
	}

	protected function get_assessment(){
		return $this->assessment;
	}
	
	protected function create_map(){
		$result = parent::create_map();
		$result['prompt'] = 'div'; 
		$result['itemBody'] = 'span'; 
		return $result;
	}

	protected function interaction_to_text($interaction){
		$answers = array();
    	$responses = $this->strategy->get_possible_responses($this->assessment, $interaction);
    	foreach($responses as $response){
			$value = $this->get_answer_text($response);
	        $score = $this->get_score($interaction, $response);
		    $feedback = $this->get_feedback($interaction, $response);
		    $answers[] = $this->format_answer($value, $feedback, $score);
    	}
    	$result = $this->format_question($answers);
		$result = $this->get_doc()->createTextNode($result);
		return $result;
	}
	
	protected function process_inlineChoiceInteraction(ImsXmlReader $interaction, $prefix = '', $deep = true){
		$answers = array();
		$choices = $interaction->all_inlineChoice();
    	foreach($choices as $choice){            
    		$answer = $choice->identifier;
			$value = $this->get_answer_text($choice);
	        $feedback = $this->get_feedback($interaction, $answer);
	        $score = $this->get_score($interaction, $answer);
		    $answers[] = $this->format_answer($value, $feedback, $score);
    	}
    	$result = $this->format_question($answers);
		$result = $this->get_doc()->createTextNode($result);
		return $result;
	}
	
	protected function process_choiceInteraction(ImsXmlReader $interaction, $prefix = '', $deep = true){
		$answers = array();
		$choices = $interaction->all_simpleChoice();
    	foreach($choices as $choice){            
    		$answer = $choice->identifier;
			$value = $this->get_answer_text($choice);
	        $feedback = $this->get_feedback($interaction, $answer);
	        $score = $this->get_score($interaction, $answer);
		    $answers[] = $this->format_answer($value, $feedback, $score);
    	}
    	$result = $this->format_question($answers);
		$result = $this->get_doc()->createTextNode($result);
		return $result;
	}
	
	protected function process_textEntryInteraction(ImsXmlReader $item, $prefix = '', $deep = true){
		return $this->interaction_to_text($item);
	}	
	
	protected function process_extendedTextInteraction(ImsXmlReader $item, $prefix = '', $deep = true){
		return $this->interaction_to_text($item);
	}
	
	protected function process_gapMatchInteraction(ImsXmlReader $item, $prefix = '', $deep = true){
		return $this->interaction_to_text($item);
	}
	
	protected function get_answer_text($answer){
		if($answer instanceof ImsXmlReader){
			return $this->strategy->to_text($answer);
		}else{
			return $answer;	
		}
	}
	
	protected function format_answer($answer, $feedback, $score){
		$score = empty($score) ? '=0' : "=$score";
		$feedback = empty($feedback) ? '' : "($feedback)";
		$result = $answer.$feedback.$score;
		return $result;
	}
	
	protected function format_question(array $answers){
		$answers = $this->remove_duplicates($answers);
		$result = implode(',', $answers);
		return "[$result]";
	}

	protected function get_feedback($interaction, $answer){
		$result = implode("\n", $this->strategy->get_feedbacks($this->get_assessment(), $interaction, $answer));
		$result = html_trim_tag($result, 'span', 'p');
		return $result;
	}
	
	protected function get_score($interaction, $answer){
		$result = $this->strategy->get_score($this->get_assessment(), $interaction, $answer);
		return $result;
	}
	
	/**
	 * Duplicates are possible because of differences between QTI and Chamilo.
	 * For example numerical answers can have different tolerances.
	 * @param array $answers
	 * @return array
	 */
	protected function remove_duplicates($answers){
		$result = array();
		foreach($answers as $answer){
			$duplicate = false;
			foreach($result as $item){
				if($answer == $item){
					$duplicate = true;
					break;
				}
			}
			if(!$duplicate){
				$result[] = $answer;
			}
		}
		return $result;
	}
}

















