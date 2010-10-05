<?php

/**
 * $Id: dokeos185_link_category.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */
require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class represents an old Dokeos 1.8.5 Link Category
 *
 * @author David Van Wayenbergh
 */
class Dokeos185LinkCategory extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'link_category';
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
    function Dokeos185LinkCategory($defaultProperties = array())
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
    function is_valid()
    {
        $course = $this->get_course();

        if (!$this->get_id() || !($this->get_category_title() || $this->get_description())) {
            $this->create_failed_element($this->get_id());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'link_category', 'ID' => $this->get_id())));
            return false;
        }
        return true;
    }

    /**
     * Convert to new link category
     * @param Course $course the course
     * @return the new link category
     */
    function convert_data()
    {
        $course = $this->get_course();

        $new_course_code = $this->get_id_reference($course->get_code(), 'main_database.course');

        $chamilo_link_publication_category = new ContentObjectPublicationCategory();

        $chamilo_link_publication_category->set_course($new_course_code);

        $chamilo_link_publication_category->set_tool('link');

        if (!$this->get_category_title())
            $chamilo_link_publication_category->set_name($this->get_description());
        else
            $chamilo_link_publication_category->set_name($this->get_category_title());

        $chamilo_link_publication_category->set_parent(0); //no subcategories in dokeos 1.8.5

        $chamilo_link_publication_category->create();

        $this->create_id_reference($this->get_id(), $chamilo_link_publication_category->get_id());

        return $chamilo_link_publication_category;
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