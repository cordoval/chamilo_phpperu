<?php
/**
 * $Id: dokeos185_blog.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_blog.class.php';

/**
 * This class presents a Dokeos185 blog
 *
 * @author Sven Vanpoucke
 */
class Dokeos185Blog extends MigrationDataClass
{
    private static $mgdm;
    
    /**
     * Dokeos185Blog properties
     */
    const PROPERTY_BLOG_ID = 'blog_id';
    const PROPERTY_BLOG_NAME = 'blog_name';
    const PROPERTY_BLOG_SUBTITLE = 'blog_subtitle';
    const PROPERTY_DATE_CREATION = 'date_creation';
    const PROPERTY_VISIBILITY = 'visibility';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185Blog object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185Blog($defaultProperties = array ())
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
        return array(self :: PROPERTY_BLOG_ID, self :: PROPERTY_BLOG_NAME, self :: PROPERTY_BLOG_SUBTITLE, self :: PROPERTY_DATE_CREATION, self :: PROPERTY_VISIBILITY);
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
     * Returns the blog_id of this Dokeos185Blog.
     * @return the blog_id.
     */
    function get_blog_id()
    {
        return $this->get_default_property(self :: PROPERTY_BLOG_ID);
    }

    /**
     * Returns the blog_name of this Dokeos185Blog.
     * @return the blog_name.
     */
    function get_blog_name()
    {
        return $this->get_default_property(self :: PROPERTY_BLOG_NAME);
    }

    /**
     * Returns the blog_subtitle of this Dokeos185Blog.
     * @return the blog_subtitle.
     */
    function get_blog_subtitle()
    {
        return $this->get_default_property(self :: PROPERTY_BLOG_SUBTITLE);
    }

    /**
     * Returns the date_creation of this Dokeos185Blog.
     * @return the date_creation.
     */
    function get_date_creation()
    {
        return $this->get_default_property(self :: PROPERTY_DATE_CREATION);
    }

    /**
     * Returns the visibility of this Dokeos185Blog.
     * @return the visibility.
     */
    function get_visibility()
    {
        return $this->get_default_property(self :: PROPERTY_VISIBILITY);
    }

    /**
     * Check if the blog is valid
     * @param array $array the parameters for the validation
     * @return true if the blog is valid 
     */
    function is_valid($array)
    {
        $course = $array['course'];
    }

    /**
     * Convert to new blog
     * @param array $array the parameters for the conversion
     * @return the new blog
     */
    function convert_data
    {
        $course = $array['course'];
    }

    /**
     * Retrieve all blogs from the database
     * @param array $parameters parameters for the retrieval
     * @return array of blogs
     */
    static function retrieve_data($parameters)
    {
        self :: $mgdm = $parameters['mgdm'];
        
        $db = $parameters['course']->get_db_name();
        $tablename = 'blog';
        $classname = 'Dokeos185Blog';
        
        return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'blog';
        return $array;
    }
}

?>