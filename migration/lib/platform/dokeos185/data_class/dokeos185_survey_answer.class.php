<?php

/**
 * $Id: dokeos185_survey_answer.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */
require_once dirname(__FILE__) . "/../dokeos185_course_data_migration_data_class.class.php";

/**
 * This class presents a Dokeos185 survey_answer
 *
 * @author Sven Vanpoucke
 */
class Dokeos185SurveyAnswer extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'survey_answer';
    /**
     * Dokeos185SurveyAnswer properties
     */
    const PROPERTY_ANSWER_ID = 'answer_id';
    const PROPERTY_SURVEY_ID = 'survey_id';
    const PROPERTY_QUESTION_ID = 'question_id';
    const PROPERTY_OPTION_ID = 'option_id';
    const PROPERTY_VALUE = 'value';
    const PROPERTY_USER = 'user';

    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185SurveyAnswer object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185SurveyAnswer($defaultProperties = array())
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
        return array(self :: PROPERTY_ANSWER_ID, self :: PROPERTY_SURVEY_ID, self :: PROPERTY_QUESTION_ID, self :: PROPERTY_OPTION_ID, self :: PROPERTY_VALUE, self :: PROPERTY_USER);
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
     * Returns the answer_id of this Dokeos185SurveyAnswer.
     * @return the answer_id.
     */
    function get_answer_id()
    {
        return $this->get_default_property(self :: PROPERTY_ANSWER_ID);
    }

    /**
     * Returns the survey_id of this Dokeos185SurveyAnswer.
     * @return the survey_id.
     */
    function get_survey_id()
    {
        return $this->get_default_property(self :: PROPERTY_SURVEY_ID);
    }

    /**
     * Returns the question_id of this Dokeos185SurveyAnswer.
     * @return the question_id.
     */
    function get_question_id()
    {
        return $this->get_default_property(self :: PROPERTY_QUESTION_ID);
    }

    /**
     * Returns the option_id of this Dokeos185SurveyAnswer.
     * @return the option_id.
     */
    function get_option_id()
    {
        return $this->get_default_property(self :: PROPERTY_OPTION_ID);
    }

    /**
     * Returns the value of this Dokeos185SurveyAnswer.
     * @return the value.
     */
    function get_value()
    {
        return $this->get_default_property(self :: PROPERTY_VALUE);
    }

    /**
     * Returns the user of this Dokeos185SurveyAnswer.
     * @return the user.
     */
    function get_user()
    {
        return $this->get_default_property(self :: PROPERTY_USER);
    }

    /**
     * Checks if a surveyanswer is valid
     * @param Array $array
     * @return Boolean
     */
    function is_valid()
    {
        if (!$this->get_value())
        {
            $this->create_failed_element($this->get_answer_id());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'survey_answer', 'ID' => $this->get_answer_id())));

            return false;
        }
        return true;
    }

    /**
     * migrate surveyanswer, sets category
     * @param Array $array
     * @return LearningStyleSurveyAnswer
     */
    function convert_data()
    {
        $course = $this->get_course();

        $new_user_id = $this->get_id_reference($this->get_user(), 'main_database.user');
        $new_course_code = $this->get_id_reference($course->get_code(), 'main_database.course');

        if (!$new_user_id)
        {
            $new_user_id = $this->get_owner($new_course_code);
        }

        //survey parameters
        $chamilo_survey_user_answer = new SurveyQuestionAnswerTracker();

        //set properties
        //create announcement in database
        $chamilo_survey_user_answer->create_all();


        return $chamilo_survey_user_answer;
    }

    public static function get_class_name()
    {
        return self :: CLASS_NAME;
    }

    public static function get_table_name()
    {
        return self :: TABLE_NAME;
    }

}

?>