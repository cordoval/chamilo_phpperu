<?php

/**
 * $Id: dokeos185_tool_intro.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */
require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class presents a Dokeos185 tool_intro
 *
 * @author Sven Vanpoucke
 */
class Dokeos185ToolIntro extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'tool_intro';
    /**
     * Dokeos185ToolIntro properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_INTRO_TEXT = 'intro_text';

    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;
    /**
     * Map from id to correct chamilo tool
     * @todo add remaining mappings
     */
    private $convert = array('course_homepage' => 'home');

    /**
     * Creates a new Dokeos185ToolIntro object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185ToolIntro($defaultProperties = array())
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
        return array(self :: PROPERTY_ID, self :: PROPERTY_INTRO_TEXT);
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
     * Returns the id of this Dokeos185ToolIntro.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the intro_text of this Dokeos185ToolIntro.
     * @return the intro_text.
     */
    function get_intro_text()
    {
        return $this->get_default_property(self :: PROPERTY_INTRO_TEXT);
    }

    /**
     * Checks if a tool intro is valid
     * @param Array $array
     * @return Boolean
     */
    function is_valid()
    {
        if (!$this->get_intro_text()) {
            $this->create_failed_element($this->get_id());
            return false;
        }
        return true;
    }

    /**
     * Convert to description, set category, make publication
     * @param Array $array
     * @return Description
     */
    function convert_data()
    {
        $new_course_id = $this->get_id_reference($this->get_course()->get_code(), 'main_database.course');
        $owner_id = $this->get_data_manager()->get_owner_id();

        $chamilo_tool_intro = new Introduction();
        $chamilo_tool_intro->set_title($this->get_intro_text());

        $chamilo_tool_intro->set_description($this->get_intro_text());

        // Category for contents already exists?
        $chamilo_repository_category_id = RepositoryDataManager::get_repository_category_by_name_or_create_new($owner_id, 'descriptions');
        $chamilo_tool_intro->set_parent_id($chamilo_repository_category_id);

        $chamilo_tool_intro->set_owner_id($owner_id);
        $chamilo_tool_intro->create();

        //$this->create_publication($$chamilo_tool_intro, $new_course_code, $owner_id, $tool, 'description');

        $publication = new ContentObjectPublication();
        $publication->set_content_object($chamilo_tool_intro);
        $publication->set_content_object_id($chamilo_tool_intro->get_id());
        $publication->set_course_id($new_course_id);
        $publication->set_publisher_id($owner_id);
        $publication->set_tool($this->convert[$this->get_id()]);

        $publication->set_category_id(0);
        $publication->set_from_date(0);
        $publication->set_to_date(0);
        $publication->set_publication_date(0);
        $publication->set_modified_date(0);
        //$publication->set_modified_date(0);
        //$publication->set_display_order_index($this->get_display_order());
        $publication->set_display_order_index(0);

        $publication->set_email_sent($this->get_email_sent());

        $publication->create();
        //create publication in database
        

        return $chamilo_tool_intro;
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