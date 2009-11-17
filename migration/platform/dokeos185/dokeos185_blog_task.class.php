<?php
/**
 * $Id: dokeos185_blog_task.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_blog_task.class.php';

/**
 * This class presents a Dokeos185 blog_task
 *
 * @author Sven Vanpoucke
 */
class Dokeos185BlogTask extends ImportBlogTask
{
    private static $mgdm;
    
    /**
     * Dokeos185BlogTask properties
     */
    const PROPERTY_TASK_ID = 'task_id';
    const PROPERTY_BLOG_ID = 'blog_id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_COLOR = 'color';
    const PROPERTY_SYSTEM_TASK = 'system_task';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185BlogTask object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185BlogTask($defaultProperties = array ())
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
        return array(self :: PROPERTY_TASK_ID, self :: PROPERTY_BLOG_ID, self :: PROPERTY_TITLE, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_COLOR, self :: PROPERTY_SYSTEM_TASK);
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
     * Returns the task_id of this Dokeos185BlogTask.
     * @return the task_id.
     */
    function get_task_id()
    {
        return $this->get_default_property(self :: PROPERTY_TASK_ID);
    }

    /**
     * Returns the blog_id of this Dokeos185BlogTask.
     * @return the blog_id.
     */
    function get_blog_id()
    {
        return $this->get_default_property(self :: PROPERTY_BLOG_ID);
    }

    /**
     * Returns the title of this Dokeos185BlogTask.
     * @return the title.
     */
    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * Returns the description of this Dokeos185BlogTask.
     * @return the description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Returns the color of this Dokeos185BlogTask.
     * @return the color.
     */
    function get_color()
    {
        return $this->get_default_property(self :: PROPERTY_COLOR);
    }

    /**
     * Returns the system_task of this Dokeos185BlogTask.
     * @return the system_task.
     */
    function get_system_task()
    {
        return $this->get_default_property(self :: PROPERTY_SYSTEM_TASK);
    }

    /**
     * Check if the blog task is valid
     * @param array $array the parameters for the validation
     */
    function is_valid($array)
    {
        $course = $array['course'];
    }

    /**
     * Convert to new blog task
     * @param array $array the parameters for the conversion
     */
    function convert_to_lcms($array)
    {
        $course = $array['course'];
    }

    /**
     * Retrieve all blog tasks from the database
     * @param array $parameters parameters for the retrieval
     */
    static function get_all($parameters)
    {
        self :: $mgdm = $parameters['mgdm'];
        
        $db = $parameters['course']->get_db_name();
        $tablename = 'blog_task';
        $classname = 'Dokeos185BlogTask';
        
        return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'blog_task';
        return $array;
    }
}

?>