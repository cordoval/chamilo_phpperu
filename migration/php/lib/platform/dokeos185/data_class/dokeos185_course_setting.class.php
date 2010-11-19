<?php

namespace migration;
/**
 * $Id: dokeos185_course_setting.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */
require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class presents a dokeos185 course_setting
 *
 * @author Sven Vanpoucke
 */
class Dokeos185CourseSetting extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'course_setting';

    /**
     * Dokeos185CourseSetting properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_VARIABLE = 'variable';
    const PROPERTY_SUBKEY = 'subkey';
    const PROPERTY_TYPE = 'type';
    const PROPERTY_CATEGORY = 'category';
    const PROPERTY_VALUE = 'value';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_COMMENT = 'comment';
    const PROPERTY_SUBKEYTEXT = 'subkeytext';

    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185CourseSetting object
     * @param array $defaultProperties The default properties
     */
    function __construct($defaultProperties = array())
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
        return array(self :: PROPERTY_ID, self :: PROPERTY_VARIABLE, self :: PROPERTY_SUBKEY, self :: PROPERTY_TYPE, self :: PROPERTY_CATEGORY, self :: PROPERTY_VALUE, self :: PROPERTY_TITLE, self :: PROPERTY_COMMENT, self :: PROPERTY_SUBKEYTEXT);
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
     * Returns the id of this Dokeos185CourseSetting.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Sets the id of this Dokeos185CourseSetting.
     * @param id
     */
    function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    /**
     * Returns the variable of this Dokeos185CourseSetting.
     * @return the variable.
     */
    function get_variable()
    {
        return $this->get_default_property(self :: PROPERTY_VARIABLE);
    }

    /**
     * Sets the variable of this Dokeos185CourseSetting.
     * @param variable
     */
    function set_variable($variable)
    {
        $this->set_default_property(self :: PROPERTY_VARIABLE, $variable);
    }

    /**
     * Returns the subkey of this Dokeos185CourseSetting.
     * @return the subkey.
     */
    function get_subkey()
    {
        return $this->get_default_property(self :: PROPERTY_SUBKEY);
    }

    /**
     * Sets the subkey of this Dokeos185CourseSetting.
     * @param subkey
     */
    function set_subkey($subkey)
    {
        $this->set_default_property(self :: PROPERTY_SUBKEY, $subkey);
    }

    /**
     * Returns the type of this Dokeos185CourseSetting.
     * @return the type.
     */
    function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }

    /**
     * Sets the type of this Dokeos185CourseSetting.
     * @param type
     */
    function set_type($type)
    {
        $this->set_default_property(self :: PROPERTY_TYPE, $type);
    }

    /**
     * Returns the category of this Dokeos185CourseSetting.
     * @return the category.
     */
    function get_category()
    {
        return $this->get_default_property(self :: PROPERTY_CATEGORY);
    }

    /**
     * Sets the category of this Dokeos185CourseSetting.
     * @param category
     */
    function set_category($category)
    {
        $this->set_default_property(self :: PROPERTY_CATEGORY, $category);
    }

    /**
     * Returns the value of this Dokeos185CourseSetting.
     * @return the value.
     */
    function get_value()
    {
        return $this->get_default_property(self :: PROPERTY_VALUE);
    }

    /**
     * Sets the value of this Dokeos185CourseSetting.
     * @param value
     */
    function set_value($value)
    {
        $this->set_default_property(self :: PROPERTY_VALUE, $value);
    }

    /**
     * Returns the title of this Dokeos185CourseSetting.
     * @return the title.
     */
    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * Sets the title of this Dokeos185CourseSetting.
     * @param title
     */
    function set_title($title)
    {
        $this->set_default_property(self :: PROPERTY_TITLE, $title);
    }

    /**
     * Returns the comment of this Dokeos185CourseSetting.
     * @return the comment.
     */
    function get_comment()
    {
        return $this->get_default_property(self :: PROPERTY_COMMENT);
    }

    /**
     * Sets the comment of this Dokeos185CourseSetting.
     * @param comment
     */
    function set_comment($comment)
    {
        $this->set_default_property(self :: PROPERTY_COMMENT, $comment);
    }

    /**
     * Returns the subkeytext of this Dokeos185CourseSetting.
     * @return the subkeytext.
     */
    function get_subkeytext()
    {
        return $this->get_default_property(self :: PROPERTY_SUBKEYTEXT);
    }

    /**
     * Sets the subkeytext of this Dokeos185CourseSetting.
     * @param subkeytext
     */
    function set_subkeytext($subkeytext)
    {
        $this->set_default_property(self :: PROPERTY_SUBKEYTEXT, $subkeytext);
    }

    /**
     * Check if the course setting is valid
     * @param array $array the parameters for the validation
     * @return true if the course setting is valid 
     */
    function is_valid()
    {

        if (!$this->get_id() || !($this->get_variable() || $this->get_category()))
        {
            $this->create_failed_element($this->get_id());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'course_setting', 'ID' => $this->get_id())));

            return false;
        }
        return true;
    }

    /**
     * Convert to new course setting
     * @param array $array the parameters for the conversion
     * @return the new course setting
     * @todo implementation
     */
    function convert_data()
    {
        $chamilo_setting = new Setting();
        $chamilo_setting->set_variable($this->get_variable());
        $chamilo_setting->set_value($this->get_value());
        $chamilo_setting->set_application($this->get_category());
        //
        //$chamilo_setting->create();
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