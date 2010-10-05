<?php
/**
 * $Id: dokeos185_quiz_question.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class presents a Dokeos185 quiz_question
 *
 * @author Sven Vanpoucke
 */
class Dokeos185QuizQuestion extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'quiz_question';
    
    /**
     * Dokeos185QuizQuestion properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_QUESTION = 'question';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_PONDERATION = 'ponderation';
    const PROPERTY_POSITION = 'position';
    const PROPERTY_TYPE = 'type';
    const PROPERTY_PICTURE = 'picture';
 
    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_QUESTION, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_PONDERATION, self :: PROPERTY_POSITION, self :: PROPERTY_TYPE, self :: PROPERTY_PICTURE);
    }

    /**
     * Returns the id of this Dokeos185QuizQuestion.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the question of this Dokeos185QuizQuestion.
     * @return the question.
     */
    function get_question()
    {
        return $this->get_default_property(self :: PROPERTY_QUESTION);
    }

    /**
     * Returns the description of this Dokeos185QuizQuestion.
     * @return the description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Returns the ponderation of this Dokeos185QuizQuestion.
     * @return the ponderation.
     */
    function get_ponderation()
    {
        return $this->get_default_property(self :: PROPERTY_PONDERATION);
    }

    /**
     * Returns the position of this Dokeos185QuizQuestion.
     * @return the position.
     */
    function get_position()
    {
        return $this->get_default_property(self :: PROPERTY_POSITION);
    }

    /**
     * Returns the type of this Dokeos185QuizQuestion.
     * @return the type.
     */
    function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }

    /**
     * Returns the picture of this Dokeos185QuizQuestion.
     * @return the picture.
     */
    function get_picture()
    {
        return $this->get_default_property(self :: PROPERTY_PICTURE);
    }

    /**
     * Checks if a quizquestion is valid
     * @return Boolean
     */
    function is_valid()
    {
        if (! $this->get_id() || ! $this->get_type() || ! $this->get_question() || $this->get_type() == 6)
        {
            $this->create_failed_element($this->get_id());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'quiz_question', 'ID' => $this->get_id())));
            return false;
        }
        return true;
    }

    /**
     * migrate quizquestion, sets category
     */
    function convert_data()
    {
		$course = $this->get_course();
        
    	//$new_user_id = $this->get_id_reference($this->get_item_property()->get_insert_user_id(), 'main_database.user');
        $new_course_code = $this->get_id_reference($course->get_code(), 'main_database.course');
		$new_user_id = $this->get_data_manager()->get_owner_id($new_course_code);
        
        //sort of quiz question
        $type = $this->get_type();
        
        switch ($type)
        {
            case 1 :
                $chamilo_question = new AssessmentMultipleChoiceQuestion();
                $chamilo_question->set_answer_type(AssessmentMultipleChoiceQuestion :: ANSWER_TYPE_RADIO);
                break;
            case 2 :
                $chamilo_question = new AssessmentMultipleChoiceQuestion();
                $chamilo_question->set_answer_type(AssessmentMultipleChoiceQuestion :: ANSWER_TYPE_CHECKBOX);
                break;
            case 3 :
                $chamilo_question = new FillInBlanksQuestion();
                break;
            case 4 :
                $chamilo_question = new AssessmentMatchingQuestion();
                break;
            default :
                $chamilo_question = new AssessmentOpenQuestion();
                break;
        }
        
        $chamilo_category_id = RepositoryDataManager :: get_repository_category_by_name_or_create_new($new_user_id, Translation :: get('Assessments'));
        $chamilo_question->set_parent_id($chamilo_category_id);
        
        $chamilo_question->set_title($this->get_question());
        
        if (! $this->get_description())
        {
            $chamilo_question->set_description($this->get_question());
        }
        else
        {
            $chamilo_question->set_description($this->get_description());
        }
        
        $chamilo_question->set_owner_id($new_user_id);
        $chamilo_question->create();
        
        // Retrieve all the connections to the different quizzes and convert them because we need to store ponderation and position as well
        $quiz_rel_questions = $this->get_data_manager()->retrieve_quiz_rel_questions($course, $this->get_id());
        while($quiz_rel_question = $quiz_rel_questions->next_result())
        {
        	$quiz_rel_question->set_course($this->get_course());
        	
        	if($quiz_rel_question->is_relation_valid($chamilo_question->get_id(), $this->get_ponderation()))
        	{
        		$quiz_rel_question->convert_relation_data($chamilo_question->get_id(), $this->get_ponderation());
        	}
        }
        
        $this->create_id_reference($this->get_id(), $chamilo_question->get_id());
        $this->set_message(Translation :: get('GeneralConvertedMessage', array('TYPE' => 'quiz_question', 'OLD_ID' => $this->get_id(), 'NEW_ID' => $chamilo_question->get_id())));
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