<?php
/**
 * $Id: dokeos185_shared_survey_question.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_shared_survey_question.class.php';

/**
 * This class presents a Dokeos185 shared_survey_question
 *
 * @author Sven Vanpoucke
 */
class Dokeos185SharedSurveyQuestion extends MigrationDataClass
{
    private static $mgdm;
    
    /**
     * Dokeos185SharedSurveyQuestion properties
     */
    const PROPERTY_QUESTION_ID = 'question_id';
    const PROPERTY_SURVEY_ID = 'survey_id';
    const PROPERTY_SURVEY_QUESTION = 'survey_question';
    const PROPERTY_SURVEY_QUESTION_COMMENT = 'survey_question_comment';
    const PROPERTY_TYPE = 'type';
    const PROPERTY_DISPLAY = 'display';
    const PROPERTY_SORT = 'sort';
    const PROPERTY_CODE = 'code';
    const PROPERTY_MAX_VALUE = 'max_value';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185SharedSurveyQuestion object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185SharedSurveyQuestion($defaultProperties = array ())
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
        return array(self :: PROPERTY_QUESTION_ID, self :: PROPERTY_SURVEY_ID, self :: PROPERTY_SURVEY_QUESTION, self :: PROPERTY_SURVEY_QUESTION_COMMENT, self :: PROPERTY_TYPE, self :: PROPERTY_DISPLAY, self :: PROPERTY_SORT, self :: PROPERTY_CODE, self :: PROPERTY_MAX_VALUE);
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
     * Returns the question_id of this Dokeos185SharedSurveyQuestion.
     * @return the question_id.
     */
    function get_question_id()
    {
        return $this->get_default_property(self :: PROPERTY_QUESTION_ID);
    }

    /**
     * Returns the survey_id of this Dokeos185SharedSurveyQuestion.
     * @return the survey_id.
     */
    function get_survey_id()
    {
        return $this->get_default_property(self :: PROPERTY_SURVEY_ID);
    }

    /**
     * Returns the survey_question of this Dokeos185SharedSurveyQuestion.
     * @return the survey_question.
     */
    function get_survey_question()
    {
        return $this->get_default_property(self :: PROPERTY_SURVEY_QUESTION);
    }

    /**
     * Returns the survey_question_comment of this Dokeos185SharedSurveyQuestion.
     * @return the survey_question_comment.
     */
    function get_survey_question_comment()
    {
        return $this->get_default_property(self :: PROPERTY_SURVEY_QUESTION_COMMENT);
    }

    /**
     * Returns the type of this Dokeos185SharedSurveyQuestion.
     * @return the type.
     */
    function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }

    /**
     * Returns the display of this Dokeos185SharedSurveyQuestion.
     * @return the display.
     */
    function get_display()
    {
        return $this->get_default_property(self :: PROPERTY_DISPLAY);
    }

    /**
     * Returns the sort of this Dokeos185SharedSurveyQuestion.
     * @return the sort.
     */
    function get_sort()
    {
        return $this->get_default_property(self :: PROPERTY_SORT);
    }

    /**
     * Returns the code of this Dokeos185SharedSurveyQuestion.
     * @return the code.
     */
    function get_code()
    {
        return $this->get_default_property(self :: PROPERTY_CODE);
    }

    /**
     * Returns the max_value of this Dokeos185SharedSurveyQuestion.
     * @return the max_value.
     */
    function get_max_value()
    {
        return $this->get_default_property(self :: PROPERTY_MAX_VALUE);
    }

    /**
     * Checks if a shared survey question is valid
     * @param Array $array
     * @return Boolean
     */
    function is_valid($array)
    {
    
    }

    /**
     * migrate sharedsurveyquestion, sets category
     * @param Array $array
     * @return
     */
    function convert_data
    {
    
    }

    /**
     * Gets all the shared survey questions of a course
     * @param Array $array
     * @return Array of dokeos185sharedsurveyquestion
     */
    static function retrieve_data($parameters)
    {
        self :: $mgdm = $parameters['mgdm'];
        
        $db = 'main_database';
        $tablename = 'shared_survey_question';
        $classname = 'Dokeos185SharedSurveyQuestion';
        
        return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = 'main_database';
        $array['table'] = 'shared_survey_question';
        return $array;
    }
}

?>