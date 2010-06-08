<?php

/**
 * $Id: dokeos185_link_category.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_link_category.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/category_manager/content_object_publication_category.class.php';

/**
 * This class represents an old Dokeos 1.8.5 Link Category
 *
 * @author David Van Wayenbergh
 */

class Dokeos185LinkCategory extends ImportLinkCategory
{
    private static $mgdm;
    
    /**
     * link category properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_CATEGORY_TITLE = 'category_title';
    const PROPERTY_DESCRIPTION = 'description';
    
    /**
     * Default properties of the link category object, stored in an associative
     * array.
     */
    private $defaultProperties;

    /**
     * Creates a new link category object.
     * @param array $defaultProperties The default properties of the link category
     *                                 object. Associative array.
     */
    function Dokeos185LinkCategory($defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets a default property of this link category object by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gets the default properties of this link category.
     * @return array An associative array containing the properties.
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    /**
     * Get the default properties of all link categories.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_CATEGORY_TITLE, self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Sets a default property of this link category by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Sets the default properties of this link.
     * @param array $defaultProperties An associative array containing the properties.
     */
    function set_default_properties($defaultProperties)
    {
        return $this->defaultProperties = $defaultProperties;
    }

    /**
     * Returns the id of this link category.
     * @return int The id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the category_title of this link category.
     * @return String The category_title.
     */
    function get_category_title()
    {
        return $this->get_default_property(self :: PROPERTY_CATEGORY_TITLE);
    }

    /**
     * Returns the description of this link category.
     * @return String The description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Check if the link category is valid
     * @param Course $course the course
     * @return true if the link category is valid 
     */
    function is_valid($array)
    {
        $course = $array['course'];
        $mgdm = MigrationDataManager :: get_instance();
        if (! $this->get_id() || ! ($this->get_category_title() || $this->get_description()))
        {
            $mgdm->add_failed_element($this->get_id(), $course->get_db_name() . '.link');
            return false;
        }
        return true;
    }

    /**
     * Convert to new link category
     * @param Course $course the course
     * @return the new link category
     */
    function convert_to_lcms($array)
    {
        $course = $array['course'];
        $mgdm = MigrationDataManager :: get_instance();
        $new_course_code = $mgdm->get_id_reference($course->get_code(), 'weblcms_course');
        
        $lcms_link_category = new ContentObjectPublicationCategory();
        
        $lcms_link_category->set_course($new_course_code);
        $lcms_link_category->set_tool('link');
        
        if (! $this->get_category_title())
            $lcms_link_category->set_name($this->get_description());
        else
            $lcms_link_category->set_name($this->get_category_title());
        
        $lcms_link_category->get_parent(0);
        
        $lcms_link_category->create();
        
        $mgdm->add_id_reference($this->get_id(), $lcms_link_category->get_id(), $new_course_code . '.link_category');
        
        return $lcms_link_category;
    
    }

    /**
     * Retrieve all link categories from the database
     * @param array $parameters parameters for the retrieval
     * @return array of link categories
     */
    static function get_all($parameters)
    {
        $old_mgdm = $parameters['old_mgdm'];
        
        $coursedb = $parameters['course']->get_db_name();
        $tablename = 'link_category';
        $classname = 'Dokeos185LinkCategory';
        
        return $old_mgdm->get_all($coursedb, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'link_category';
        return $array;
    }
}
?>