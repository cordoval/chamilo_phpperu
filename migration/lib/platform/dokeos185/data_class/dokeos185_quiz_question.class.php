<?php
/**
 * $Id: dokeos185_quiz_question.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_quiz_question.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/content_object/fill_in_blanks_question/fill_in_blanks_question.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/content_object/matching_question/matching_question.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/content_object/multiple_choice_question/multiple_choice_question.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/content_object/open_question/open_question.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/content_object_publication.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/content_object/category/category.class.php';
require_once dirname(__FILE__) . '/dokeos185_quiz_answer.class.php';

/**
 * This class presents a Dokeos185 quiz_question
 *
 * @author Sven Vanpoucke
 */
class Dokeos185QuizQuestion extends MigrationDataClass
{
    /** 
     * Migration data manager
     */
    private static $mgdm;
    
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
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185QuizQuestion object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185QuizQuestion($defaultProperties = array ())
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
        return array(self :: PROPERTY_ID, self :: PROPERTY_QUESTION, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_PONDERATION, self :: PROPERTY_POSITION, self :: PROPERTY_TYPE, self :: PROPERTY_PICTURE);
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
     * Gets all the quizquestion of a course
     * @param Array $array
     * @return Array of dokeos185quizquestion
     */
    static function retrieve_data($parameters)
    {
        $old_mgdm = $parameters['old_mgdm'];
        
        if ($parameters['del_files'] = ! 1)
            $tool_name = 'quiz_question';
        
        $coursedb = $parameters['course']->get_db_name();
        $tablename = 'quiz_question';
        $classname = 'Dokeos185QuizQuestion';
        
        return $old_mgdm->get_all($coursedb, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'quiz_question';
        return $array;
    }

    /**
     * Checks if a quizquestion is valid
     * @param Array $array
     * @return Boolean
     */
    function is_valid($array)
    {
        $course = $array['course'];
        $mgdm = MigrationDataManager :: get_instance();
        if (! $this->get_id() || ! $this->get_type() || ! $this->get_question() || ! $this->get_position())
        {
            $mgdm->add_failed_element($this->get_id(), $course->get_db_name() . '.quiz_question');
            return false;
        }
        return true;
    }

    /**
     * migrate quizquestion, sets category
     * @param Array $array
     * @return 
     */
    function convert_data
    {
        $old_mgdm = $array['old_mgdm'];
        $mgdm = MigrationDataManager :: get_instance();
        $course = $array['course'];
        $new_course_code = $mgdm->get_id_reference($course->get_code(), 'weblcms_course');
        
        $answers = array();
        $answers = $old_mgdm->get_all_question_answer($course->get_db_name(), $this->get_id());
        $new_user_id = $mgdm->get_owner($course->get_code());
        
        //sort of quiz question
        $type = $this->get_type();
        
        switch ($type)
        {
            case 1 :
                $lcms_question = new MultipleChoiceQuestion();
                break;
            case 2 :
                $lcms_question = new MultipleChoiceQuestion();
                break;
            case 3 :
                $lcms_question = new FillInBlanksQuestion();
                //$lcms_question = $answers[0];
                break;
            case 4 :
                $lcms_question = new MatchingQuestion();
                break;
            default :
                $lcms_question = new OpenQuestion();
                break;
        }
        
        // Category for quiz questions already exists?
        $lcms_category_id = $mgdm->get_parent_id($new_user_id, 'category', Translation :: get('quizzes'));
        if (! $lcms_category_id)
        {
            //Create category for tool in lcms
            $lcms_repository_category = new Category();
            $lcms_repository_category->set_owner_id($new_user_id);
            $lcms_repository_category->set_title(Translation :: get('quizzes'));
            $lcms_repository_category->set_description('...');
            
            //Retrieve repository id from course
            $repository_id = $mgdm->get_parent_id($new_user_id, 'category', Translation :: get('MyRepository'));
            $lcms_repository_category->set_parent_id($repository_id);
            
            //Create category in database
            $lcms_repository_category->create();
            
            $lcms_question->set_parent_id($lcms_repository_category->get_id());
        }
        else
        {
            $lcms_question->set_parent_id($lcms_category_id);
        }
        
        $lcms_question->set_title($this->get_question());
        
        if (! $this->get_description())
            $lcms_question->set_description($this->get_question());
        else
            $lcms_question->set_description($this->get_description());
        
        $lcms_question->set_owner_id($new_user_id);
        $lcms_question->set_display_order_index($this->get_position());
        
        //create announcement in database
        $lcms_question->create();
        
        /*
		//publication
		if($this->item_property->get_visibility() <= 1) 
		{
			$publication = new ContentObjectPublication();
			
			$publication->set_content_object($lcms_announcement);
			$publication->set_course_id($new_course_code);
			$publication->set_publisher_id($new_user_id);
			$publication->set_tool('announcement');
			$publication->set_category_id(0);
			//$publication->set_from_date(self :: $mgdm->make_unix_time($this->item_property->get_start_visible()));
			//$publication->set_to_date(self :: $mgdm->make_unix_time($this->item_property->get_end_visible()));
			$publication->set_from_date(0);
			$publication->set_to_date(0);
			$publication->set_publication_date(self :: $mgdm->make_unix_time($this->item_property->get_insert_date()));
			$publication->set_modified_date(self :: $mgdm->make_unix_time($this->item_property->get_lastedit_date()));
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
        return $lcms_question;
    }
}

?>