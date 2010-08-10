<?php

/**
 * $Id: dokeos185_track_eattempt.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */
require_once dirname(__FILE__) . '/../dokeos185_migration_data_class.class.php';

/**
 * This class presents a Dokeos185 track_e_attempt
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackEAttempt extends Dokeos185MigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'track_e_attempt';
    const DATABASE_NAME = 'statistics_database';

    /**
     * Dokeos185TrackEAttempt properties
     */
    const PROPERTY_EXE_ID = 'exe_id';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_QUESTION_ID = 'question_id';
    const PROPERTY_ANSWER = 'answer';
    const PROPERTY_TEACHER_COMMENT = 'teacher_comment';
    const PROPERTY_MARKS = 'marks';
    const PROPERTY_COURSE_CODE = 'course_code';
    const PROPERTY_POSITION = 'position';
    const PROPERTY_TMS = 'tms';

    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185TrackEAttempt object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185TrackEAttempt($defaultProperties = array())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets a default property by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gets the default properties
     * @return array An associative array containing the properties.
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_EXE_ID, self :: PROPERTY_USER_ID, self :: PROPERTY_QUESTION_ID, self :: PROPERTY_ANSWER, self :: PROPERTY_TEACHER_COMMENT, self :: PROPERTY_MARKS, self :: PROPERTY_COURSE_CODE, self :: PROPERTY_POSITION, self :: PROPERTY_TMS);
    }

    /**
     * Sets a default property by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Sets the default properties of this class
     */
    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Returns the exe_id of this Dokeos185TrackEAttempt.
     * @return the exe_id.
     */
    function get_exe_id()
    {
        return $this->get_default_property(self :: PROPERTY_EXE_ID);
    }

    /**
     * Returns the user_id of this Dokeos185TrackEAttempt.
     * @return the user_id.
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Returns the question_id of this Dokeos185TrackEAttempt.
     * @return the question_id.
     */
    function get_question_id()
    {
        return $this->get_default_property(self :: PROPERTY_QUESTION_ID);
    }

    /**
     * Returns the answer of this Dokeos185TrackEAttempt.
     * @return the answer.
     */
    function get_answer()
    {
        return $this->get_default_property(self :: PROPERTY_ANSWER);
    }

    /**
     * Returns the teacher_comment of this Dokeos185TrackEAttempt.
     * @return the teacher_comment.
     */
    function get_teacher_comment()
    {
        return $this->get_default_property(self :: PROPERTY_TEACHER_COMMENT);
    }

    /**
     * Returns the marks of this Dokeos185TrackEAttempt.
     * @return the marks.
     */
    function get_marks()
    {
        return $this->get_default_property(self :: PROPERTY_MARKS);
    }

    /**
     * Returns the course_code of this Dokeos185TrackEAttempt.
     * @return the course_code.
     */
    function get_course_code()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_CODE);
    }

    /**
     * Returns the position of this Dokeos185TrackEAttempt.
     * @return the position.
     */
    function get_position()
    {
        return $this->get_default_property(self :: PROPERTY_POSITION);
    }

    /**
     * Returns the tms of this Dokeos185TrackEAttempt.
     * @return the tms.
     */
    function get_tms()
    {
        return $this->get_default_property(self :: PROPERTY_TMS);
    }

    /**
     * Validation checks
     * @param Array $array
     * @todo
     */
    function is_valid()
    {
        if (!$this->get_answer() || !$this->get_tms())
        {
            $this->create_failed_element($this->get_id());
            return false;
        }
        return true;
    }

    /**
     * Convertion
     * @param Array $array
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

    function get_database_name()
    {
        return self :: DATABASE_NAME;
    }

}
?>