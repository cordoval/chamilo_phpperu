<?php
namespace repository\content_object\survey;

use repository\RepositoryDataManager;
use common\libraries\Request;
use common\libraries\FormValidator;
use common\libraries\Translation;
use common\libraries\Configuration;
use common\libraries\Utilities;
use common\libraries\Path;
use repository\content_object\survey_page\SurveyPage;

class ConfigureQuestionForm extends FormValidator {

	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;
	const RESULT_SUCCESS = 'QuestionConfigurationUpdated';
	const RESULT_ERROR = 'QuestionConfigurationUpdateFailed';

	private $survey_page;
	private $complex_question;

	function __construct($form_type, $action, $complex_item, $page_id) {
		parent::__construct ( 'create_context_template', 'post', $action );
		Request::set_get( SurveyBuilder::PARAM_SURVEY_PAGE_ID, $page_id );

//		$this->question = RepositoryDataManager::get_instance ()->retrieve_content_object ( $complex_item->get_ref () );
		$this->complex_question = $complex_item;
		$this->survey_page = RepositoryDataManager::get_instance ()->retrieve_content_object ( $page_id );

		$this->form_type = $form_type;
		if ($this->form_type == self::TYPE_EDIT) {
			$this->build_editing_form ();
		} elseif ($this->form_type == self::TYPE_CREATE) {
			$this->build_creation_form ();
		}

		$this->setDefaults ();
	}

	function build_basic_form() {

		$question_display = SurveyQuestionDisplay::factory ( $this, $this->complex_question, $answer );
		$question_display->display ();
		//    	$this->addElement('text', SurveyContextTemplate :: PROPERTY_NAME, Translation :: get('Name'), array("size" => "50"));
		//        $this->addRule(SurveyContextTemplate :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');


		$this->addElement ( 'multiselect', SurveyPage:: TO_VISIBLE_QUESTIONS_IDS, Translation::get ( 'Questions' ), $this->get_questions () , array('style'=> 'width: 250px'));
		$this->addRule ( SurveyPage:: TO_VISIBLE_QUESTIONS_IDS, Translation::get ( 'ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES ), 'required' );

	}

	function build_editing_form() {
		//        $template = $this->template;
	//        $parent = $this->parent;
	//
	//        $this->build_basic_form();
	//
	//        $this->addElement('hidden', SurveyContextTemplate :: PROPERTY_ID);
	//
	//        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
	//        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
	//
	//        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
	}

	function build_creation_form() {
		$this->build_basic_form ();

		$buttons [] = $this->createElement ( 'style_submit_button', 'submit', Translation::get ( 'Create', null, Utilities::COMMON_LIBRARIES ), array ('class' => 'positive' ) );
		$buttons [] = $this->createElement ( 'style_reset_button', 'reset', Translation::get ( 'Reset', null, Utilities::COMMON_LIBRARIES), array ('class' => 'normal empty' ) );

		$this->addGroup ( $buttons, 'buttons', null, '&nbsp;', false );
	}

	function update_config() {

		return $value;
	}

	function create_config() {

		     $values = $this->exportValues();
					    
		     $configs = $this->survey_page->get_config();

		     $config = array();

			 $config[SurveyPage :: FROM_VISIBLE_QUESTION_ID] = $this->complex_question->get_id();
			 $config[SurveyPage::TO_VISIBLE_QUESTIONS_IDS] = $values[SurveyPage::TO_VISIBLE_QUESTIONS_IDS];
		     $keys = array_keys($values);
		     $answers = array();
		     foreach ($keys as $key) {
		     	$ids = explode( '_', $key);
		     	if($ids[0]== $this->complex_question->get_id()){
		     		$answers[$key] = $values[$key];
		     	}
		     }
		     $config[SurveyPage:: ANSWERMATCHES] = $answers;

		    
		     $duplicat = false;

		     foreach ($configs as $conf){
		     	$answer_diff = array_diff($config[SurveyPage:: ANSWERMATCHES], $conf[SurveyPage:: ANSWERMATCHES]);

		     	$same_from_id = $config[SurveyPage:: FROM_VISIBLE_QUESTION_ID] == $conf[SurveyPage:: FROM_VISIBLE_QUESTION_ID];

		     	$to_ids_diff = array_diff($config[SurveyPage:: TO_VISIBLE_QUESTIONS_IDS], $conf[SurveyPage:: TO_VISIBLE_QUESTIONS_IDS]);

		     	if($same_from_id && count($answer_diff) ==0 && count($to_ids_diff) ==0 ){
		     		$duplicat = true;
		     	}

		     }
					     
		     if(!$duplicat){
		     	$configs[] = $config;
		     	$this->survey_page->set_config($configs);
		     	$this->survey_page->update();
		     }

	}

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ()) {

	}

	function get_config() {

	}

	function get_questions() {
		$complex_question_items = $this->survey_page->get_questions(true);
		$questions = array();
		while ($complex_question_item = $complex_question_items->next_result()) {
			if($complex_question_item->get_visible()== 0){
				$question = RepositoryDataManager::get_instance ()->retrieve_content_object ( $complex_question_item->get_ref ());
				$id = $question->get_id();
				$questions[$complex_question_item->get_id()] = Utilities :: truncate_string($id.' : '.$question->get_title(), 40);
			}
		}
		return $questions;
	}
}
?>