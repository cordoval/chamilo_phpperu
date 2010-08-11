<?php
/**
 * $Id: dokeos185_quiz_answer.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class presents a Dokeos185 quiz_answer
 *
 * @author Sven Vanpouckes
 */
class Dokeos185QuizAnswer extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'quiz_answer';
    
    /**
     * Dokeos185QuizAnswer properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_QUESTION_ID = 'question_id';
    const PROPERTY_ANSWER = 'answer';
    const PROPERTY_CORRECT = 'correct';
    const PROPERTY_COMMENT = 'comment';
    const PROPERTY_PONDERATION = 'ponderation';
    const PROPERTY_POSITION = 'position';
    const PROPERTY_HOTSPOT_COORDINATES = 'hotspot_coordinates';
    const PROPERTY_HOTSPOT_TYPE = 'hotspot_type';
    
    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_QUESTION_ID, self :: PROPERTY_ANSWER, self :: PROPERTY_CORRECT, self :: PROPERTY_COMMENT, self :: PROPERTY_PONDERATION, self :: PROPERTY_POSITION, self :: PROPERTY_HOTSPOT_COORDINATES, self :: PROPERTY_HOTSPOT_TYPE);
    }

    /**
     * Returns the id of this Dokeos185QuizAnswer.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the question_id of this Dokeos185QuizAnswer.
     * @return the question_id.
     */
    function get_question_id()
    {
        return $this->get_default_property(self :: PROPERTY_QUESTION_ID);
    }

    /**
     * Returns the answer of this Dokeos185QuizAnswer.
     * @return the answer.
     */
    function get_answer()
    {
        return $this->get_default_property(self :: PROPERTY_ANSWER);
    }

    /**
     * Returns the correct of this Dokeos185QuizAnswer.
     * @return the correct.
     */
    function get_correct()
    {
        return $this->get_default_property(self :: PROPERTY_CORRECT);
    }

    /**
     * Returns the comment of this Dokeos185QuizAnswer.
     * @return the comment.
     */
    function get_comment()
    {
        return $this->get_default_property(self :: PROPERTY_COMMENT);
    }

    /**
     * Returns the ponderation of this Dokeos185QuizAnswer.
     * @return the ponderation.
     */
    function get_ponderation()
    {
        return $this->get_default_property(self :: PROPERTY_PONDERATION);
    }

    /**
     * Returns the position of this Dokeos185QuizAnswer.
     * @return the position.
     */
    function get_position()
    {
        return $this->get_default_property(self :: PROPERTY_POSITION);
    }

    /**
     * Returns the hotspot_coordinates of this Dokeos185QuizAnswer.
     * @return the hotspot_coordinates.
     */
    function get_hotspot_coordinates()
    {
        return $this->get_default_property(self :: PROPERTY_HOTSPOT_COORDINATES);
    }

    /**
     * Returns the hotspot_type of this Dokeos185QuizAnswer.
     * @return the hotspot_type.
     */
    function get_hotspot_type()
    {
        return $this->get_default_property(self :: PROPERTY_HOTSPOT_TYPE);
    }

	/**
     * Check if the object is valid
     */
    function is_valid()
    {
    	$new_question_id = $this->get_id_reference($this->get_question_id(), $this->get_database_name() . '.quiz_question');
    	
    	if (! $this->get_id() || ! $new_question_id || ! $this->get_answer())
        {
            $this->create_failed_element($this->get_id());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'quiz_answer', 'ID' => $this->get_id())));
            return false;
        }
        return true;
    }

    /**
     * Convert
     */
    function convert_data()
    {
    	$new_question_id = $this->get_id_reference($this->get_question_id(), $this->get_database_name() . '.quiz_question');
    	$question = RepositoryDataManager :: get_instance()->retrieve_content_object($new_question_id);
    	switch($question->get_type())
    	{
    		case AssessmentMultipleChoiceQuestion :: get_type_name():
    			$this->convert_multiple_choice_question($question);
    			break;
    		case FillInBlanksQuestion :: get_type_name():
    			$this->convert_fill_in_blanks_question($question);
    			break;
    		case AssessmentMatchingQuestion :: get_type_name():
    			$this->convert_matching_question($question);
    			break;
    	}
    	
    	$this->set_message(Translation :: get('GeneralConvertedMessage', array('TYPE' => 'quiz_answer', 'OLD_ID' => $this->get_id(), 'NEW_ID' => $question->get_id())));
    }
    
    /**
     * Converts multiple choice questions
     * @param AssessmentMultipleChoiceQuestion $question
     */
    private function convert_multiple_choice_question(AssessmentMultipleChoiceQuestion $question)
    {
    	$option = new AssessmentMultipleChoiceQuestionOption($this->get_answer(), $this->get_correct(), $this->get_ponderation(), $this->get_comment());
    	$question->add_option($option);
    	$question->update();
    }
    
	/**
     * Converts fill in the blanks questions
     * @param FillInBlanksQuestion $question
     */
    private function convert_fill_in_blanks_question(FillInBlanksQuestion $question)
    {
    	$answer_text = $this->get_answer();
    	$question->set_question_type(FillInBlanksQuestion :: TYPE_TEXT);
    	
		$split = explode('::', $answer_text);
		$scores = explode(',', $split[1]);
		$answer_text = $split[0];
		
		$pattern = '/\[[^\[\]]*\]/';
		$answers = preg_match_all($pattern, $answer_text, $matches);
		
		foreach($matches[0] as $i => $answer)
		{
			$score = $scores[$i] ? $scores[$i] : 0;
			
			$answer = substr($answer, 1, -1);
			$new_answer = '[' . $answer . '=' . $score . ']';
			$answer_text = preg_replace('/\[' . $answer . '\]/', $new_answer, $answer_text, 1, $count);
		}
    	$question->set_answer_text($answer_text);
    	$question->update();
    }
    
	/**
     * Converts matching questions
     * @param AssessmentMatchingQuestion $question
     */
    private function convert_matching_question(AssessmentMatchingQuestion $question)
    {
    	if($this->get_correct())
    	{
    		$option = new AssessmentMatchingQuestionOption($this->get_answer(), $this->get_correct() - 1, $this->get_ponderation());
    		$question->add_option($option);
    	}
    	else
    	{
			$matches = $question->get_matches();
			$matches[$this->get_position() - 1] = $this->get_answer();
			$question->set_matches($matches);
    	}
    	
    	$question->update();
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
    
    static function get_class_name()
    {
    	return self :: CLASS_NAME;
    }
}

?>