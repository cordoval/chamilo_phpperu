<?php
/**
 * $Id: dokeos185_blog_rel_user.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_blog_rel_user.class.php';

/**
 * This class presents a Dokeos185 blog_rel_user
 *
 * @author Sven Vanpoucke
 */
class Dokeos185BlogRelUser extends ImportBlogRelUser
{
    private static $mgdm;
    
    /**
     * Dokeos185BlogRelUser properties
     */
    const PROPERTY_BLOG_ID = 'blog_id';
    const PROPERTY_USER_ID = 'user_id';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185BlogRelUser object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185BlogRelUser($defaultProperties = array ())
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
        return array(self :: PROPERTY_BLOG_ID, self :: PROPERTY_USER_ID);
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
     * Returns the blog_id of this Dokeos185BlogRelUser.
     * @return the blog_id.
     */
    function get_blog_id()
    {
        return $this->get_default_property(self :: PROPERTY_BLOG_ID);
    }

    /**
     * Returns the user_id of this Dokeos185BlogRelUser.
     * @return the user_id.
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Check if the blog user relation is valid
     * @param array $array the parameters for the validation
     */
    function is_valid($array)
    {
        $course = $array['course'];
    }

    /**
     * Convert to new blog user relation
     * @param array $array the parameters for the conversion
     */
    function convert_to_lcms($array)
    {
        $course = $array['course'];
    }

    /**
     * Retrieve all blog user relations from the database
     * @param array $parameters parameters for the retrieval
     */
    static function get_all($parameters)
    {
        self :: $mgdm = $parameters['mgdm'];
        
        $db = $parameters['course']->get_db_name();
        $tablename = 'blog_post_rel_user';
        $classname = 'Dokeos185BlogPostRelUser';
        
        return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'blog_post_rel_user';
        return $array;
    }
}

?>