<?php

require_once dirname(__FILE__) . "/../dokeos185_course_data_migration_data_class.class.php";
/**
 * $Id: dokeos185_survey_question.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 survey_question
 *
 * @author Sven Vanpoucke
 */
class Dokeos185SurveyQuestion extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'survey_question';
    /**
     * Dokeos185SurveyQuestion properties
     */
    const PROPERTY_QUESTION_ID = 'question_id';
    const PROPERTY_SURVEY_ID = 'survey_id';
    const PROPERTY_SURVEY_QUESTION = 'survey_question';
    const PROPERTY_SURVEY_QUESTION_COMMENT = 'survey_question_comment';
    const PROPERTY_TYPE = 'type';
    const PROPERTY_DISPLAY = 'display';
    const PROPERTY_SORT = 'sort';
    const PROPERTY_SHARED_QUESTION_ID = 'shared_question_id';
    const PROPERTY_MAX_VALUE = 'max_value';

    const TYPE_YESNO = 'yesno';
    const TYPE_SCORE = 'score';
    const TYPE_MULTIPLE_RESPONSE = 'multipleresponse';
    const TYPE_MULTIPLE_CHOICE = 'multiplechoice';
    const TYPE_OPEN = 'open';
    const TYPE_DROPDOWN = 'dropdown';
    const TYPE_PERCENTAGE = 'percentage';
    const TYPE_FEEDBACK = 'comment';
    const TYPE_PAGEBREAK = 'pagebreak';
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185SurveyQuestion object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185SurveyQuestion($defaultProperties = array())
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
        return array(self :: PROPERTY_QUESTION_ID, self :: PROPERTY_SURVEY_ID, self :: PROPERTY_SURVEY_QUESTION, self :: PROPERTY_SURVEY_QUESTION_COMMENT, self :: PROPERTY_TYPE, self :: PROPERTY_DISPLAY, self :: PROPERTY_SORT, self :: PROPERTY_SHARED_QUESTION_ID, self :: PROPERTY_MAX_VALUE);
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
     * Returns the question_id of this Dokeos185SurveyQuestion.
     * @return the question_id.
     */
    function get_question_id()
    {
        return $this->get_default_property(self :: PROPERTY_QUESTION_ID);
    }

    /**
     * Returns the survey_id of this Dokeos185SurveyQuestion.
     * @return the survey_id.
     */
    function get_survey_id()
    {
        return $this->get_default_property(self :: PROPERTY_SURVEY_ID);
    }

    /**
     * Returns the survey_question of this Dokeos185SurveyQuestion.
     * @return the survey_question.
     */
    function get_survey_question()
    {
        return $this->get_default_property(self :: PROPERTY_SURVEY_QUESTION);
    }

    /**
     * Returns the survey_question_comment of this Dokeos185SurveyQuestion.
     * @return the survey_question_comment.
     */
    function get_survey_question_comment()
    {
        return $this->get_default_property(self :: PROPERTY_SURVEY_QUESTION_COMMENT);
    }

    /**
     * Returns the type of this Dokeos185SurveyQuestion.
     * @return the type.
     */
    function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }

    /**
     * Returns the display of this Dokeos185SurveyQuestion.
     * @return the display.
     */
    function get_display()
    {
        return $this->get_default_property(self :: PROPERTY_DISPLAY);
    }

    /**
     * Returns the sort of this Dokeos185SurveyQuestion.
     * @return the sort.
     */
    function get_sort()
    {
        return $this->get_default_property(self :: PROPERTY_SORT);
    }

    /**
     * Returns the shared_question_id of this Dokeos185SurveyQuestion.
     * @return the shared_question_id.
     */
    function get_shared_question_id()
    {
        return $this->get_default_property(self :: PROPERTY_SHARED_QUESTION_ID);
    }

    /**
     * Returns the max_value of this Dokeos185SurveyQuestion.
     * @return the max_value.
     */
    function get_max_value()
    {
        return $this->get_default_property(self :: PROPERTY_MAX_VALUE);
    }

    /**
     * Checks if a surveyquestion is valid
     * @param Array $array
     * @return Boolean
     */
    function is_valid()
    {
        if (!$this->get_survey_question())
        {
            $this->create_failed_element($this->get_question_id());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'survey_question', 'ID' => $this->get_question_id())));

            return false;
        }
        return true;
    }

    /**
     * migrate surveyquestion, sets category
     * @param Array $array
     * @return LearningStyleSurveyQuestion
     */
    function convert_data()
    {
        $new_course_code = $this->get_id_reference($this->get_course()->get_code(), 'main_database.course');
        //$new_user_id = $this->get_data_manager()->get_owner_id($new_course_code);
        //temporary until getowner works
        $new_user_id = $this->get_id_reference($this->get_survey_id(), $this->get_database_name() . '.survey.temp_user');

        //survey to which the question is attached (already migrated)
        $survey_id = $this->get_id_reference($this->get_survey_id(), $this->get_database_name() . '.survey');

        $survey = RepositoryDataManager::get_instance()->retrieve_content_object($survey_id);

        //the current_page holds the page of the survey that we'll be adding content to
        $pages = $survey->get_pages();
        $pages_count = count($pages);
        
        if ($pages_count > 0)
            $current_page = $pages[count($pages) - 1];
        else
            $current_page = $this->create_new_survey_page($new_user_id, $survey_id, 'first page');

        //survey question parameters
        $chamilo_survey_question = null;
        switch ($this->get_type())
        {
            case self :: TYPE_YESNO :
                $chamilo_survey_question = new SurveyMultipleChoiceQuestion();
                $chamilo_survey_question->set_answer_type(SurveyMultipleChoiceQuestion::ANSWER_TYPE_RADIO);
                break;
            case self :: TYPE_MULTIPLE_CHOICE :
                $chamilo_survey_question = new SurveyMultipleChoiceQuestion();
                $chamilo_survey_question->set_answer_type(SurveyMultipleChoiceQuestion::ANSWER_TYPE_RADIO);
                break;
            case self :: TYPE_MULTIPLE_RESPONSE :
                $chamilo_survey_question = new SurveyMultipleChoiceQuestion();
                $chamilo_survey_question->set_answer_type(SurveyMultipleChoiceQuestion::ANSWER_TYPE_CHECKBOX);
                break;
            case self :: TYPE_OPEN :
                $chamilo_survey_question = new SurveyOpenQuestion();
                break;
            case self :: TYPE_DROPDOWN :
                $chamilo_survey_question = new SurveySelectQuestion();
                break;
            case self :: TYPE_PERCENTAGE :
                $chamilo_survey_question = new SurveyRatingQuestion();
                break;
            case self :: TYPE_FEEDBACK :
                $chamilo_survey_question = new SurveyDescription();
                break;
            case self :: TYPE_PAGEBREAK :
                $current_page = $this->create_new_survey_page($new_user_id, $survey_id, $this->get_survey_question());
                return null; // not a real chamilo answertype
                break;
            default :
                $chamilo_survey_question = new SurveyDescription();
                break;
        }

        //set the additional properties

        $repository_category_id = RepositoryDataManager::get_repository_category_by_name_or_create_new($new_user_id, 'Surveys');
        $chamilo_survey_question->set_parent_id($repository_category_id);

        $chamilo_survey_question->set_title($this->get_survey_question());

        $chamilo_survey_question->set_description('...');
        if ($this->get_survey_question_comment())
            $chamilo_survey_question->set_comment($this->get_survey_question_comment());

        $chamilo_survey_question->set_owner_id($new_user_id);
        //$chamilo_survey_question->set_display_order_index();

        //create announcement in database
        $chamilo_survey_question->create();

        //connect the question to the right page
        $this->create_complex_content_object_item($chamilo_survey_question, $current_page->get_id(), $new_user_id, 0);

        //create reference in migration table
        $this->create_id_reference($this->get_question_id(), $chamilo_survey_question->get_id());

        return $chamilo_survey_question;
        }

    /**
     * Creates a new page after the last one and adds it to the relevant survey
     * @param int $new_user_id the chamilo user
     * @param int $survey_id   the chamilo survey
     * @param string $title    the title of the page
     * @return SurveyPage
     */
    private function create_new_survey_page($new_user_id, $survey_id, $title = 'new migrated page')
    {
        $current_page = new SurveyPage ();
        $current_page->set_owner_id($new_user_id);
        $current_page->set_title($title);
        $current_page->set_creation_date(0);
        $current_page->set_modification_date(0);

        $current_page->create_all();

        //connect the page to the survey
        $this->create_complex_content_object_item($current_page, $survey_id, $new_user_id, 0);

        return $current_page;
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