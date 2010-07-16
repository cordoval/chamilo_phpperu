<?php

/**
 * $Id: dokeos185_course_rel_class.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */
require_once dirname(__FILE__) . '/../dokeos185_migration_data_class.class.php';
/**
 * This class represents an old Dokeos 1.8.5 course_rel_class
 *
 * @author David Van Wayenbergh
 */

class Dokeos185CourseRelClass extends Dokeos185MigrationDataClass
{
    const CLASS_NAME = __CLASS__;
	const TABLE_NAME = 'course_rel_class';   
	const DATABASE_NAME = 'main_database';
	
    /**
     * course relation class properties
     */
    const PROPERTY_COURSE_CODE = 'course_code';
    const PROPERTY_CLASS_ID = 'class_id';

    /**
     * Get the default properties of all courses.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_COURSE_CODE, self :: PROPERTY_CLASS_ID);
    }
    
    /**
     * RELATION CLASS GETTERS AND SETTERS
     */
    
    /**
     * Returns the course_code of this rel_class.
     * @return String The course_code.
     */
    function get_course_code()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_CODE);
    }

    function set_course_code($course_code)
    {
    	$this->set_default_property(self :: PROPERTY_COURSE_CODE, $course_code);
    }
    
    /**
     * Returns the class_id of this rel_class.
     * @return int The class_id.
     */
    function get_class_id()
    {
        return $this->get_default_property(self :: PROPERTY_CLASS_ID);
    }
    
	function set_class_id($class_id)
    {
    	$this->set_default_property(self :: PROPERTY_CLASS_ID, $class_id);
    }
    

    /**
     * Check if the course class relation is valid
     * @return true if the course class relation is valid 
     */
    function is_valid()
    {
        if (! $this->get_course_code() || ! $this->get_class_id() || 
        	  $this->get_failed_element($this->get_course_code(), 'main_database.course') || $this->get_failed_element($this->get_class_id(), 'main_database.class'))
        {
            $this->create_failed_element($this->get_course_id() . '-' . $this->get_class_code());
            $this->set_message(Translation :: get('CourseRelClassInvalidMessage', array('CLASS_ID' => $this->get_class_id(), 'COURSE_ID' => $this->get_course_code())));
            return false;
        }
        
        return true;
    }

    /**
     * Convert to new course class relation
     * @return the new course class relation
     */
    function convert_data()
    {
        $chamilo_course_class_relation = new CourseGroupRelation();
        
        $course_id = $this->get_id_reference($this->get_course_code(), 'main_database.course');
        if ($course_id)
        {
            $chamilo_course_class_relation->set_course_id($course_id);
        }
        
        $class_id = $this->get_id_reference($this->get_class_id(), 'main_database.class');
        if ($class_id)
        {
            $chamilo_course_class_relation->set_group_id($class_id);
        }
        
        $chamilo_course_class_relation->create();
        
        $this->set_message(Translation :: get('CourseRelClassConvertedMessage', array('CLASS_ID' => $this->get_class_id(), 'COURSE_ID' => $this->get_course_code())));
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
    
    static function get_class_name()
    {
    	return self :: CLASS_NAME;
    }
    
    static function get_database_name()
    {
    	return self :: DATABASE_NAME;
    }
}
?>