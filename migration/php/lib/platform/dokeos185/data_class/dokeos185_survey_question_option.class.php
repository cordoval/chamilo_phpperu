<?php
/**
 * $Id: dokeos185_survey_question_option.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 survey_question_option
 *
 * @author Sven Vanpoucke
 */
class Dokeos185SurveyQuestionOption extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'survey_question_option';
    
    /**
     * Dokeos185SurveyQuestionOption properties
     */
    const PROPERTY_QUESTION_OPTION_ID = 'question_option_id';
    const PROPERTY_QUESTION_ID = 'question_id';
    const PROPERTY_SURVEY_ID = 'survey_id';
    const PROPERTY_OPTION_TEXT = 'option_text';
    const PROPERTY_SORT = 'sort';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185SurveyQuestionOption object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185SurveyQuestionOption($defaultProperties = array ())
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
        return array(self :: PROPERTY_QUESTION_OPTION_ID, self :: PROPERTY_QUESTION_ID, self :: PROPERTY_SURVEY_ID, self :: PROPERTY_OPTION_TEXT, self :: PROPERTY_SORT);
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
     * Returns the question_option_id of this Dokeos185SurveyQuestionOption.
     * @return the question_option_id.
     */
    function get_question_option_id()
    {
        return $this->get_default_property(self :: PROPERTY_QUESTION_OPTION_ID);
    }

    /**
     * Returns the question_id of this Dokeos185SurveyQuestionOption.
     * @return the question_id.
     */
    function get_question_id()
    {
        return $this->get_default_property(self :: PROPERTY_QUESTION_ID);
    }

    /**
     * Returns the survey_id of this Dokeos185SurveyQuestionOption.
     * @return the survey_id.
     */
    function get_survey_id()
    {
        return $this->get_default_property(self :: PROPERTY_SURVEY_ID);
    }

    /**
     * Returns the option_text of this Dokeos185SurveyQuestionOption.
     * @return the option_text.
     */
    function get_option_text()
    {
        return $this->get_default_property(self :: PROPERTY_OPTION_TEXT);
    }

    /**
     * Returns the sort of this Dokeos185SurveyQuestionOption.
     * @return the sort.
     */
    function get_sort()
    {
        return $this->get_default_property(self :: PROPERTY_SORT);
    }

    /**
     * Checks if surveyquestionoption is valid
     * @param Array $array
     * @return Boolean 
     */
    function is_valid()
    {
        
        if (! $this->get_option_text() || !$this->get_id_reference($this->get_question_id(), $this->get_database_name() . '.survey_question'))
        {
            $this->create_failed_element($this->get_id());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'survey_question_option', 'ID' => $this->get_question_id())));
            return false;
        }
        return true;
    }

    /**
     * Convert to LearningStyleSurveyAnswer, sets category
     * @param Array $array
     * @return LearningStyleSurveyAnswer
     */
    function convert_data()
    {
        //retrieve the refered survey question
        $survey_question_id = $this->get_id_reference($this->get_question_id(), $this->get_database_name() . '.survey_question');
        $survey_question = RepositoryDataManager::get_instance()->retrieve_content_object($survey_question_id);

        //only multiple choice and select have non standard options. These need to be added
        switch (get_class($survey_question))
        {
            case SurveyMultipleChoiceQuestion::CLASS_NAME:
                $option = new SurveyMultipleChoiceQuestionOption($this->get_option_text());
                $survey_question->add_option($option);
                $survey_question->update();
                break;
            case SurveySelectQuestion::CLASS_NAME:
                $option = new SurveySelectQuestionOption($this->get_option_text());
                $survey_question->add_option($option);
                $survey_question->update();
                break;
        }

        return $survey_question;
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