<?php

/**
 * $Id: dokeos185_course_category.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */
require_once dirname(__FILE__) . '/../dokeos185_migration_data_class.class.php';
require_once Path :: get(SYS_PATH) . 'application/weblcms/php/category_manager/course_category.class.php';

/**
 * This class represents an old Dokeos 1.8.5 course_category
 *
 * @author David Van Wayenbergh
 * @author Sven Vanpoucke
 */
class Dokeos185CourseCategory extends Dokeos185MigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'course_category';
    const DATABASE_NAME = 'main_database';
    
    /**
     * course category properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_CODE = 'code';
    const PROPERTY_PARENT_ID = 'parent_id';

    /**
     * Get the default properties of all courses.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_CODE, self :: PROPERTY_PARENT_ID);
    }

    /**
     * CATEGORY GETTERS AND SETTERS
     */
    
    /**
     * Returns the ID of this category.
     * @return int The ID.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the name of this category.
     * @return String The name.
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Returns the code of this category.
     * @return String The code.
     */
    function get_code()
    {
        return $this->get_default_property(self :: PROPERTY_CODE);
    }

    /**
     * Returns the parent_id of this category.
     * @return String The parent_id.
     */
    function get_parent_id()
    {
        return $this->get_default_property(self :: PROPERTY_PARENT_ID);
    }

    /**
     * Sets the code of this category.
     * @param String $code The code.
     */
    function set_code($code)
    {
        $this->set_default_property(self :: PROPERTY_CODE, $code);
    }

    /**
     * Check if the course category is valid
     * @return true if the course category is valid 
     */
    function is_valid()
    {
        if (! $this->get_name())
        {
            $this->create_failed_element($this->get_id());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'course category', 'ID' => $this->get_id())));
            return false;
        }
        
        return true;
    }

    /**
     * Convert to new course category
     * @return the new course category
     */
    function convert_data()
    {
        //Course category parameters
        $chamilo_course_category = new CourseCategory();
        $chamilo_course_category->set_name($this->get_name());
        
        if ($this->get_parent_id())
        {
            $parent_id = $this->get_id_reference($this->get_parent_id(), 'main_database.course_category');
            if ($parent_id)
            {
                $chamilo_course_category->set_parent($parent_id);
            }
        }
        else
        {
            $chamilo_course_category->set_parent(0);
        }
        
        //create course_category in database
        $chamilo_course_category->create();
        
        //Add id references to temp table
        $this->create_id_reference($this->get_code(), $chamilo_course_category->get_id());
        $this->set_message(Translation :: get('GeneralConvertedMessage', array('TYPE' => 'course_category', 'OLD_ID' => $this->get_id(), 'NEW_ID' => $chamilo_course_category->get_id())));
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }

    static function get_class_name()
    {
        return self :: CLASS_NAME;
    }

    function get_database_name()
    {
        return self :: DATABASE_NAME;
    }

}
?>