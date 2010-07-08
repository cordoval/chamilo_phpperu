<?php
/**
 * $Id: dokeos185_blog_rating.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_blog_rating.class.php';

/**
 * This class presents a Dokeos185 blog_rating
 *
 * @author Sven Vanpoucke
 */
class Dokeos185BlogRating extends MigrationDataClass
{
    private static $mgdm;
    
    /**
     * Dokeos185BlogRating properties
     */
    const PROPERTY_RATING_ID = 'rating_id';
    const PROPERTY_BLOG_ID = 'blog_id';
    const PROPERTY_RATING_TYPE = 'rating_type';
    const PROPERTY_ITEM_ID = 'item_id';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_RATING = 'rating';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185BlogRating object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185BlogRating($defaultProperties = array ())
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
        return array(self :: PROPERTY_RATING_ID, self :: PROPERTY_BLOG_ID, self :: PROPERTY_RATING_TYPE, self :: PROPERTY_ITEM_ID, self :: PROPERTY_USER_ID, self :: PROPERTY_RATING);
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
     * Returns the rating_id of this Dokeos185BlogRating.
     * @return the rating_id.
     */
    function get_rating_id()
    {
        return $this->get_default_property(self :: PROPERTY_RATING_ID);
    }

    /**
     * Returns the blog_id of this Dokeos185BlogRating.
     * @return the blog_id.
     */
    function get_blog_id()
    {
        return $this->get_default_property(self :: PROPERTY_BLOG_ID);
    }

    /**
     * Returns the rating_type of this Dokeos185BlogRating.
     * @return the rating_type.
     */
    function get_rating_type()
    {
        return $this->get_default_property(self :: PROPERTY_RATING_TYPE);
    }

    /**
     * Returns the item_id of this Dokeos185BlogRating.
     * @return the item_id.
     */
    function get_item_id()
    {
        return $this->get_default_property(self :: PROPERTY_ITEM_ID);
    }

    /**
     * Returns the user_id of this Dokeos185BlogRating.
     * @return the user_id.
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Returns the rating of this Dokeos185BlogRating.
     * @return the rating.
     */
    function get_rating()
    {
        return $this->get_default_property(self :: PROPERTY_RATING);
    }

    /**
     * Check if the blog rating is valid
     * @param array $array the parameters for the validation
     */
    function is_valid($array)
    {
        $course = $array['course'];
    }

    /**
     * Convert to new blog rating
     * @param array $array the parameters for the conversion
     */
    function convert_data
    {
        $course = $array['course'];
    }

    /**
     * Retrieve all blog ratings from the database
     * @param array $parameters parameters for the retrieval
     */
    static function retrieve_data($parameters)
    {
        self :: $mgdm = $parameters['mgdm'];
        
        $db = $parameters['course']->get_db_name();
        $tablename = 'blog_rating';
        $classname = 'Dokeos185BlogRating';
        
        return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'blog_rating';
        return $array;
    }
}

?>