<?php
/**
 * $Id: dokeos185_survey_question.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_survey_question.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/content_object/learning_style_survey_question/learning_style_survey_question.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/content_object_publication.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/content_object/category/category.class.php';

/**
 * This class presents a Dokeos185 survey_question
 *
 * @author Sven Vanpoucke
 */
class Dokeos185SurveyQuestion
{
    private static $mgdm;
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
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185SurveyQuestion object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185SurveyQuestion($defaultProperties = array ())
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
     * Gets all the survey questions of a course
     * @param Array $array
     * @return Array of dokeos185surveyquestion
     */
    static function get_all($parameters)
    {
        $old_mgdm = $parameters['old_mgdm'];
        
        if ($parameters['del_files'] = ! 1)
            $tool_name = 'survey_question';
        
        $coursedb = $parameters['course']->get_db_name();
        $tablename = 'survey_question';
        $classname = 'Dokeos185SurveyQuestion';
        
        return $old_mgdm->get_all($coursedb, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'survey_question';
        return $array;
    }

    /**
     * Checks if a surveyquestion is valid
     * @param Array $array
     * @return Boolean
     */
    function is_valid($array)
    {
        $course = $array['course'];
        
        if (! $this->get_survey_question())
        {
            $mgdm = MigrationDataManager :: get_instance();
            $mgdm->add_failed_element($this->get_id(), $course->get_db_name() . '.survey');
            return false;
        }
        return true;
    }

    /**
     * migrate surveyquestion, sets category
     * @param Array $array
     * @return LearningStyleSurveyQuestion
     */
    function convert_to_lcms($array)
    {
        $course = $array['course'];
        $mgdm = MigrationDataManager :: get_instance();
        
        $new_course_code = $mgdm->get_id_reference($course->get_code(), 'weblcms_course');
        $new_user_id = $mgdm->get_owner($new_course_code);
        
        //survey question parameters
        $lcms_survey_question = new LearningStyleSurveyQuestion();
        
        // Category for surveys already exists?
        $lcms_category_id = $mgdm->get_parent_id($new_user_id, 'category', Translation :: get('surveys'));
        if (! $lcms_category_id)
        {
            //Create category for tool in lcms
            $lcms_repository_category = new Category();
            $lcms_repository_category->set_owner_id($new_user_id);
            $lcms_repository_category->set_title(Translation :: get('surveys'));
            $lcms_repository_category->set_description('...');
            
            //Retrieve repository id from course
            $repository_id = $mgdm->get_parent_id($new_user_id, 'category', Translation :: get('MyRepository'));
            $lcms_repository_category->set_parent_id($repository_id);
            
            //Create category in database
            $lcms_repository_category->create();
            
            $lcms_survey_question->set_parent_id($lcms_repository_category->get_id());
        }
        else
        {
            $lcms_survey_question->set_parent_id($lcms_category_id);
        }
        
        $lcms_survey_question->set_title($this->get_survey_question());
        
        $lcms_survey_question->set_description('...');
        if ($this->get_survey_question_comment())
            $lcms_survey_question->set_comment($this->get_survey_question_comment());
        
        $lcms_survey_question->set_owner_id($new_user_id);
        $lcms_survey_question->set_display_order_index($this->get_sort());
        
        //create announcement in database
        $lcms_survey_question->create();
        
        //publication
        /*
		if($this->item_property->get_visibility() <= 1) 
		{
			$publication = new ContentObjectPublication();
			
			$publication->set_content_object($lcms_announcement);
			$publication->set_course_id($new_course_code);
			$publication->set_publisher_id($new_user_id);
			$publication->set_tool('announcement');
			$publication->set_category_id(0);
			//$publication->set_from_date($mgdm->make_unix_time($this->item_property->get_start_visible()));
			//$publication->set_to_date($mgdm->make_unix_time($this->item_property->get_end_visible()));
			$publication->set_from_date(0);
			$publication->set_to_date(0);
			$publication->set_publication_date($mgdm->make_unix_time($this->item_property->get_insert_date()));
			$publication->set_modified_date($mgdm->make_unix_time($this->item_property->get_lastedit_date()));
			//$publication->set_modified_date(0);
			//$publication->set_display_order_index($this->get_display_order());
			$publication->set_display_order_index(0);
			
			if($this->get_email_sent())
				$publication->set_email_sent($this->get_email_sent());
			else
				$publication->set_email_sent(0);
			
			$publication->set_hidden($this->item_property->get_visibility() == 1?0:1);
			
			//create publication in database
			$publication->create();
		}
		*/
        
        return $lcms_survey_question;
    }
}

?>