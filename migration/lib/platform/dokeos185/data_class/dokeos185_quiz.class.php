<?php
/**
 * $Id: dokeos185_quiz.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_quiz.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/content_object/exercise/exercise.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/content_object_publication.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/content_object/category/category.class.php';

/**
 * This class presents a Dokeos185 quiz
 *
 * @author Sven Vanpoucke
 */
class Dokeos185Quiz extends Dokeos185MigrationDataClass
{
    /** 
     * Migration data manager
     */
    private static $mgdm;
    
    /**
     * Dokeos185Quiz properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_SOUND = 'sound';
    const PROPERTY_TYPE = 'type';
    const PROPERTY_RANDOM = 'random';
    const PROPERTY_ACTIVE = 'active';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185Quiz object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185Quiz($defaultProperties = array ())
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
        return array(self :: PROPERTY_ID, self :: PROPERTY_TITLE, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_SOUND, self :: PROPERTY_TYPE, self :: PROPERTY_RANDOM, self :: PROPERTY_ACTIVE);
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
     * Returns the id of this Dokeos185Quiz.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the title of this Dokeos185Quiz.
     * @return the title.
     */
    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * Returns the description of this Dokeos185Quiz.
     * @return the description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Returns the sound of this Dokeos185Quiz.
     * @return the sound.
     */
    function get_sound()
    {
        return $this->get_default_property(self :: PROPERTY_SOUND);
    }

    /**
     * Returns the type of this Dokeos185Quiz.
     * @return the type.
     */
    function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }

    /**
     * Returns the random of this Dokeos185Quiz.
     * @return the random.
     */
    function get_random()
    {
        return $this->get_default_property(self :: PROPERTY_RANDOM);
    }

    /**
     * Returns the active of this Dokeos185Quiz.
     * @return the active.
     */
    function get_active()
    {
        return $this->get_default_property(self :: PROPERTY_ACTIVE);
    }

    /**
     * Retrieve all quizzes from the database
     * @param array $parameters parameters for the retrieval
     * @return array of quizzes
     */
    static function retrieve_data($parameters)
    {
        $old_mgdm = $parameters['old_mgdm'];
        
        if ($parameters['del_files'] = ! 1)
            $tool_name = 'quiz';
        
        $coursedb = $parameters['course']->get_db_name();
        $tablename = 'quiz';
        $classname = 'Dokeos185Quiz';
        
        return $old_mgdm->get_all($coursedb, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'quiz';
        return $array;
    }

    /**
     * Check if the quiz is valid
     * @param array $array the parameters for the validation
     * @return true if the quiz is valid 
     */
    function is_valid($array)
    {
        $course = $array['course'];
        $mgdm = MigrationDataManager :: get_instance();
        if (! $this->get_id() || ! ($this->get_title() || $this->get_description()))
        {
            $mgdm->add_failed_element($this->get_id(), $course->get_db_name() . '.quiz');
            return false;
        }
        return true;
    }

    /**
     * Convert to new quiz
     * @param array $array the parameters for the conversion
     * @return the new quiz
     */
    function convert_data
    {
        $mgdm = MigrationDataManager :: get_instance();
        $course = $array['course'];
        $new_course_code = $mgdm->get_id_reference($course->get_code(), 'weblcms_course');
        
        $new_user_id = $mgdm->get_owner($new_course_code);
        
        //forum parameters
        $lcms_exercise = new Exercise();
        
        // Category for announcements already exists?
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
            
            $lcms_exercise->set_parent_id($lcms_repository_category->get_id());
        }
        else
        {
            $lcms_exercise->set_parent_id($lcms_category_id);
        }
        
        if (! $this->get_title())
            $lcms_exercise->set_title(substr($this->get_description(), 0, 20));
        else
            $lcms_exercise->set_title($this->get_title());
        
        if (! $this->get_description())
            $lcms_exercise->set_description($this->get_title());
        else
            $lcms_exercise->set_description($this->get_description());
        
        $lcms_exercise->set_owner_id($new_user_id);
        
        //create announcement in database
        $lcms_exercise->create();
        
        //Add id references to temp table
        $mgdm->add_id_reference($this->get_id(), $lcms_exercise->get_id(), 'exercice');
        
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
        return $lcms_exercise;
    }
}

?>