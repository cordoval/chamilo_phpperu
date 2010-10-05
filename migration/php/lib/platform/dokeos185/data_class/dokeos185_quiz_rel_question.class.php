<?php
/**
 * $Id: dokeos185_quiz_rel_question.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class presents a Dokeos185 quiz_rel_question
 *
 * @author Sven Vanpoucke
 */
class Dokeos185QuizRelQuestion extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'quiz_rel_question';
    
    /**
     * Dokeos185QuizRelQuestion properties
     */
    const PROPERTY_QUESTION_ID = 'question_id';
    const PROPERTY_EXERCICE_ID = 'exercice_id';
   
    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_QUESTION_ID, self :: PROPERTY_EXERCICE_ID);
    }

    /**
     * Returns the question_id of this Dokeos185QuizRelQuestion.
     * @return the question_id.
     */
    function get_question_id()
    {
        return $this->get_default_property(self :: PROPERTY_QUESTION_ID);
    }

    /**
     * Returns the exercice_id of this Dokeos185QuizRelQuestion.
     * @return the exercice_id.
     */
    function get_exercice_id()
    {
        return $this->get_default_property(self :: PROPERTY_EXERCICE_ID);
    }

    function is_valid()
    {
    	
    }
    
    function convert_data()
    {
    	
    }
    
	/**
     * Check if the object is valid
     */
    function is_relation_valid($new_question_id, $ponderation)
    {
    	$new_exercice_id = $this->get_id_reference($this->get_exercice_id(), $this->get_database_name() . '.quiz');
    	
    	if (!$new_question_id || !$new_exercice_id || !$ponderation)
        {
            $this->create_failed_element($this->get_exercice_id() . '-' . $this->get_question_id());
            $this->set_message(Translation :: get('QuizRelQuestionInvalidMessage', array('QUIZ' => $this->get_exercice_id(), 'QUESTION' => $this->get_question_id())));
            return false;
        }
        return true;
    }

    /**
     * Convert
     */
    function convert_relation_data($new_question_id, $ponderation)
    {
    	$new_exercice_id = $this->get_id_reference($this->get_exercice_id(), $this->get_database_name() . '.quiz');
    	
    	$course = $this->get_course();
        $new_course_code = $this->get_id_reference($course->get_code(), 'main_database.course');
    	$new_user_id = $this->get_data_manager()->get_owner_id($new_course_code);
    	
    	$new_question = RepositoryDataManager :: get_instance()->retrieve_content_object($new_question_id);
    	
    	$complex_content_object_item = $this->create_complex_content_object_item($new_question, $new_exercice_id, $new_user_id, null, null, array('weight' => $ponderation));
    	$this->set_message(Translation :: get('QuizRelQuestionConvertedMessage', array('QUIZ' => $this->get_exercice_id(), 'QUESTION' => $this->get_question_id(), 'NEW_ID' => $complex_content_object_item->get_id())));
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