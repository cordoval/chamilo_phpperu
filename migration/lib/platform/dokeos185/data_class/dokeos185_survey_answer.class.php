<?php
/**
 * $Id: dokeos185_survey_answer.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_survey_answer.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/content_object/learning_style_survey_user_answer/learning_style_survey_user_answer.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/content_object_publication.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/content_object/category/category.class.php';

/**
 * This class presents a Dokeos185 survey_answer
 *
 * @author Sven Vanpoucke
 */
class Dokeos185SurveyAnswer extends Dokeos185MigrationDataClass
{
    private static $mgdm;
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
    function Dokeos185SurveyAnswer($defaultProperties = array ())
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
     * Gets all the survey answers of a course
     * @param Array $array
     * @return Array of dokeos185surveyanswer
     */
    static function retrieve_data($parameters)
    {
        $old_mgdm = $parameters['old_mgdm'];
        
        if ($parameters['del_files'] = ! 1)
            $tool_name = 'survey_answer';
        
        $coursedb = $parameters['course']->get_db_name();
        $tablename = 'survey_answer';
        $classname = 'Dokeos185SurveyAnswer';
        
        return $old_mgdm->get_all($coursedb, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'survey_answer';
        return $array;
    }

    /**
     * Checks if a surveyanswer is valid
     * @param Array $array
     * @return Boolean
     */
    function is_valid($array)
    {
        
        $course = $array['course'];
        
        if (! $this->get_value())
        {
            $mgdm = MigrationDataManager :: get_instance();
            $mgdm->add_failed_element($this->get_id(), $course->get_db_name() . '.survey_answer');
            return false;
        }
        return true;
    }

    /**
     * migrate surveyanswer, sets category
     * @param Array $array
     * @return LearningStyleSurveyAnswer
     */
    function convert_data
    {
        $course = $array['course'];
        $mgdm = MigrationDataManager :: get_instance();
        
        $new_user_id = $mgdm->get_id_reference($this->get_user(), 'user_user');
        $new_course_code = $mgdm->get_id_reference($course->get_code(), 'weblcms_course');
        if (! $new_user_id)
        {
            $new_user_id = $mgdm->get_owner($new_course_code);
        }
        
        //survey parameters
        $lcms_survey_user_answer = new LearningStyleSurveyUserAnswer();
        
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
            
            $lcms_survey_user_answer->set_parent_id($lcms_repository_category->get_id());
        }
        else
        {
            $lcms_survey_user_answer->set_parent_id($lcms_category_id);
        }
        
        $lcms_survey_user_answer->set_description($this->get_value());
        $lcms_survey_user_answer->set_title($this->get_value());
        $lcms_survey_user_answer->set_owner_id($new_user_id);
        
        //create announcement in database
        $lcms_survey_user_answer->create_all();
        
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
        return $lcms_survey_user_answer;
    }
}

?>