<?php
/**
 * $Id: dokeos185_quiz.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class presents a Dokeos185 quiz
 *
 * @author Sven Vanpoucke
 */
class Dokeos185Quiz extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'quiz';
    
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
    const PROPERTY_RESULTS_DISABLED = 'results_disabled';
    const PROPERTY_ACCESS_CONDITION = 'access_condition';
    
    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_TITLE, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_SOUND, self :: PROPERTY_TYPE, self :: PROPERTY_RANDOM, self :: PROPERTY_ACTIVE, 
        			 self :: PROPERTY_RESULTS_DISABLED, self :: PROPERTY_ACCESS_CONDITION);
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
     * Returns the results_disabled of this Dokeos185Quiz.
     * @return the results_disabled.
     */
    function get_results_disabled()
    {
        return $this->get_default_property(self :: PROPERTY_RESULTS_DISABLED);
    }
    
	/**
     * Returns the access_condition of this Dokeos185Quiz.
     * @return the access_condition.
     */
    function get_access_condition()
    {
        return $this->get_default_property(self :: PROPERTY_ACCESS_CONDITION);
    }

    /**
     * Check if the quiz is valid
     * @return true if the quiz is valid 
     */
    function is_valid()
    {
        $this->set_item_property($this->get_data_manager()->get_item_property($this->get_course(), 'quiz', $this->get_id()));
        
    	if (! $this->get_id() || ! ($this->get_title() || $this->get_description()))
        {
            $this->create_failed_element($this->get_id());
            return false;
        }
        return true;
    }

    /**
     * Convert to new quiz
     */
    function convert_data()
    {
     	$course = $this->get_course();
        
    	$new_user_id = $this->get_id_reference($this->get_item_property()->get_insert_user_id(), 'main_database.user');
        $new_course_code = $this->get_id_reference($course->get_code(), 'main_database.course');

        if (! $new_user_id)
        {
            $new_user_id = $this->get_data_manager()->get_owner_id($new_course_code);
        }
        
        //forum parameters
        $chamilo_assessment = new Assessment();
        
        $chamilo_category_id = RepositoryDataManager :: get_repository_category_by_name_or_create_new($new_user_id, Translation :: get('Assessments'));
        $chamilo_assessment->set_parent_id($chamilo_category_id);
        
        if($this->get_description())
        {
        	$description = $this->parse_text_field_for_images($this->get_description());
        	$chamilo_assessment->set_description($description);
        }
        else
        {
        	$chamilo_assessment->set_description($this->get_title());
        }
        
        if (! $this->get_title())
        {
            $chamilo_assessment->set_title(Utilities :: truncate_string($description, 20));
        }
        else
        {
            $chamilo_assessment->set_title($this->get_title());
        }
        
        $chamilo_assessment->set_owner_id($new_user_id);
        $chamilo_assessment->set_creation_date(strtotime($this->get_item_property()->get_insert_date()));
        $chamilo_assessment->set_modification_date(strtotime($this->get_item_property()->get_lastedit_date()));
        
        if ($this->get_item_property()->get_visibility() == 2)
        {
            $chamilo_assessment->set_state(1);
        }
        
		$chamilo_assessment->set_random_questions($this->get_random());
        
        $chamilo_assessment->create_all();
        $this->create_publication($chamilo_assessment, $new_course_code, $new_user_id, 'assessment');

        $this->create_id_reference($this->get_id(), $chamilo_assessment->get_id());
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