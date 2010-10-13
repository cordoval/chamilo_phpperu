<?php
namespace repository;

use common\libraries\Translation;

/**
 * $Id: questions_survey_viewer_wizard_page.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.survey.component.viewer.wizard
 */
require_once dirname ( __FILE__ ) . '/../../../../survey/component/viewer/wizard/inc/survey_question_display.class.php';

class QuestionsSurveyViewerWizardPage extends SurveyViewerWizardPage {
	private $page_number;
	private $questions;

	function QuestionsSurveyViewerWizardPage($name, $parent, $number) {
		parent::SurveyViewerWizardPage ( $name, $parent );
		$this->page_number = $number;
		//        $this->addAction('process', new SurveyViewerWizardProcess($this));


	}

	function buildForm() {

		$this->_formBuilt = true;

		$this->questions = $this->get_parent ()->get_questions ( $this->page_number );

		$question_count = count ( $this->questions );

		$survey_page = $this->get_parent ()->get_page ( $this->page_number );

		// Add buttons
		if ($this->page_number > 1) {
			$buttons [] = $this->createElement ( 'style_submit_button', $this->getButtonName ( 'back' ), Translation::get ( 'Back' ), array ('class' => 'previous' ) );
		}

		if ($this->page_number < $this->get_parent ()->get_total_pages ()) {
			$buttons [] = $this->createElement ( 'style_submit_button', $this->getButtonName ( 'next' ), Translation::get ( 'Next' ), array ('class' => 'next' ) );
			//$buttons[] = $this->createElement('style_submit_button', $this->getButtonName('submit'), Translation :: get('SaveAndFinishLater'), array('class' => 'positive'));


		} else {
			$buttons [] = $this->createElement ( 'style_submit_button', $this->getButtonName ( 'submit' ), Translation::get ( 'Submit' ), array ('class' => 'positive' ) );
		}
		$this->addGroup ( $buttons, 'buttons', null, '&nbsp;', false );

		// Add question forms


		if ($question_count != 0) {
			foreach ( $this->questions as $nr => $question ) {

				$dummy = new SurveyQuestionAnswerTracker ();
				$conditions [] = new EqualityCondition ( SurveyQuestionAnswerTracker::PROPERTY_SURVEY_PARTICIPANT_ID, $this->get_parent ()->get_parent ()->get_participant_id () );
				$conditions [] = new EqualityCondition ( SurveyQuestionAnswerTracker::PROPERTY_QUESTION_CID, $question->get_id () );
				$condition = new AndCondition ( $conditions );
				$trackers = $dummy->retrieve_tracker_items ( $condition );

				if (count ( $trackers ) != 0) {
					$answer = $trackers [0]->get_answer ();
				} else {
					$answer = null;
				}

				$visibility = $this->get_parent ()->get_question_visibility ( $question->get_id () );

				$question_display = SurveyQuestionDisplay::factory ( $this, $question, $visibility, $nr, $this->get_parent ()->get_survey (), $this->get_parent ()->get_real_page_nr ( $this->page_number ), $answer );

				$question_display->display ();
			}

			// Add buttons second time
			$this->addGroup ( $buttons, 'buttons', null, '&nbsp;', false );

		}

		$renderer = $this->defaultRenderer ();
		$renderer->setElementTemplate ( '<div style="float: right;">{element}</div><br /><br />', 'buttons' );
		$renderer->setGroupElementTemplate ( '{element}', 'buttons' );
		$this->setDefaultAction ( 'next' );

	}

	function get_page_number() {
		return $this->page_number;
	}
}
?>