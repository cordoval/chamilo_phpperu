<?php

/**
 * $Id: dokeos185_dropbox_category.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */
require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class presents a Dokeos185 dropbox_category
 *
 * @author Sven Vanpoucke
 */
class Dokeos185DropboxCategory extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'dropbox_category';

    /**
     * Dokeos185DropboxCategory properties
     */
    const PROPERTY_CAT_ID = 'cat_id';
    const PROPERTY_CAT_NAME = 'cat_name';
    const PROPERTY_RECEIVED = 'received';
    const PROPERTY_SENT = 'sent';
    const PROPERTY_USER_ID = 'user_id';

    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185DropboxCategory object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185DropboxCategory($defaultProperties = array())
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
        return array(self :: PROPERTY_CAT_ID, self :: PROPERTY_CAT_NAME, self :: PROPERTY_RECEIVED, self :: PROPERTY_SENT, self :: PROPERTY_USER_ID);
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
     * Returns the cat_id of this Dokeos185DropboxCategory.
     * @return the cat_id.
     */
    function get_cat_id()
    {
        return $this->get_default_property(self :: PROPERTY_CAT_ID);
    }

    /**
     * Returns the cat_name of this Dokeos185DropboxCategory.
     * @return the cat_name.
     */
    function get_cat_name()
    {
        return $this->get_default_property(self :: PROPERTY_CAT_NAME);
    }

    /**
     * Returns the received of this Dokeos185DropboxCategory.
     * @return the received.
     */
    function get_received()
    {
        return $this->get_default_property(self :: PROPERTY_RECEIVED);
    }

    /**
     * Returns the sent of this Dokeos185DropboxCategory.
     * @return the sent.
     */
    function get_sent()
    {
        return $this->get_default_property(self :: PROPERTY_SENT);
    }

    /**
     * Returns the user_id of this Dokeos185DropboxCategory.
     * @return the user_id.
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Sets the code of this category.
     * @param String $code The code.
     */
    function set_cat_id($code)
    {
        $this->set_default_property(self :: PROPERTY_CAT_ID, $code);
    }

    /**
     * Check if the dropbox category is valid
     * @param array $array the parameters for the validation
     * @return true if the dropbox category is valid
     */
    function is_valid()
    {
        if (!$this->get_cat_name()) {
            $this->create_failed_element($this->get_cat_id());
            return false;
        }

        return true;
    }

    /**
     * Convert to new dropbox category
     * @param array $array the parameters for the conversion
     * @return the new dropbox category
     */
    function convert_data()
    {
        //Course category parameters
        $chamilo_course_dropbox_category = new ContentObjectPublicationCategory();

        $course = $this->get_course();
        $new_course_code = $this->get_id_reference($course->get_code(), 'main_database.course');

        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_NAME, Translation :: get('Dropbox'));
        $conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_COURSE, $new_course_code);
        $conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_TOOL, 'document');
        $conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_PARENT, 0);

        $condition = new AndCondition($conditions);

        $category = WeblcmsDataManager::get_instance()->retrieve_content_object_publication_categories($condition)->next_result();

        $chamilo_course_dropbox_category->set_name($this->get_cat_name());
        $chamilo_course_dropbox_category->set_parent($category->get_id());
        $chamilo_course_dropbox_category->set_course($new_course_code);
        $chamilo_course_dropbox_category->set_tool('document');

        //create course_category in database
        $chamilo_course_dropbox_category->create();

        //Add id references to temp table
        $this->create_id_reference($this->get_cat_id(), $chamilo_course_dropbox_category->get_id());

        return $chamilo_course_dropbox_category;
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