<?php
/**
 * $Id: dokeos185_tool.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_tool.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/weblcms_data_manager.class.php';

/**
 * This class represents an old Dokeos 1.8.5 Tool
 *
 * @author Van Wayenbergh David
 */
class Dokeos185Tool extends Dokeos185MigrationDataClass
{
    /**
     * Migration data manager
     */
    private static $mgdm;
    
    private $convert = array('course_description' => 'description', 'calendar_event' => 'calendar', 'document' => 'document', 'learnpath' => 'learning_path', 'link' => 'link', 'announcement' => 'announcement', 'forum' => 'forum', 'dropbox' => 'dropbox', 'user' => 'user', 'group' => 'group', 'chat' => 'chat', 'tracking' => 'statics', 'course_setting' => 'course_settings', 'survey' => 'learning_style_survey', 'course_maintenance' => 'maintenance');
    
    /**
     * Announcement properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_VISIBILITY = 'visibility';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new dokeos185 Tool object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185Tool($defaultProperties = array ())
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
        return array(self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_VISIBILITY);
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
     * Returns the id of this tool.
     * @return int The id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the name of this tool.
     * @return string the name.
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Returns the visibility of this tool.
     * @return int the visibility.
     */
    function get_visibility()
    {
        return $this->get_default_property(self :: PROPERTY_VISIBILITY);
    }

    /**
     * Checks if tool is valid
     */
    function is_valid($array)
    {
        return isset($this->convert[$this->get_name()]);
    }

    /**
     * migrate to new tool
     * @param String $course
     * @return dokeos185tool
     */
    function convert_data
    {
        $course = $array['course'];
        $value = $this->convert[$this->get_name()];
        $db = WeblcmsDataManager :: get_instance();
        $db->set_module_visible($course->get->get_title(), $value, $this->get_visibility);
        
        return $this;
    }

    /**
     * Get all the tools of a course
     * @param Array $parameters
     * @return Array of dokeos185tool
     */
    static function retrieve_data($parameters)
    {
        $old_mgdm = $parameters['old_mgdm'];
        
        $db = $parameters['course']->get_db_name();
        $tablename = 'tool';
        $classname = 'Dokeos185Tool';
        
        return $old_mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'tool';
        return $array;
    }
}
?>