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
    	return false;
    }

    /**
     * Convert
     */
    function convert_data()
    {
    	
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