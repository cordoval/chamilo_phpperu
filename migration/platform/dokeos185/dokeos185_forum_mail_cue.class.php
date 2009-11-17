<?php
/**
 * $Id: dokeos185_forum_mail_cue.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_forum_mail_cue.class.php';

/**
 * This class presents a Dokeos185 forum_mailcue
 *
 * @author Sven Vanpoucke
 */
class Dokeos185ForumMailcue extends ImportForumMailcue
{
    private static $mgdm;
    
    /**
     * Dokeos185ForumMailcue properties
     */
    const PROPERTY_THREAD_ID = 'thread_id';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_POST_ID = 'post_id';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185ForumMailcue object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185ForumMailcue($defaultProperties = array ())
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
        return array(self :: PROPERTY_THREAD_ID, self :: PROPERTY_USER_ID, self :: PROPERTY_POST_ID);
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
     * Returns the thread_id of this Dokeos185ForumMailcue.
     * @return the thread_id.
     */
    function get_thread_id()
    {
        return $this->get_default_property(self :: PROPERTY_THREAD_ID);
    }

    /**
     * Returns the user_id of this Dokeos185ForumMailcue.
     * @return the user_id.
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Returns the post_id of this Dokeos185ForumMailcue.
     * @return the post_id.
     */
    function get_post_id()
    {
        return $this->get_default_property(self :: PROPERTY_POST_ID);
    }

    /**
     * Check if the forum mailcue is valid
     * @param array $array the parameters for the validation
     * @return true if the forum mailcue is valid 
     */
    function is_valid($array)
    {
        $course = $array['course'];
    }

    /**
     * Convert to new forum mailcue
     * @param array $array the parameters for the conversion
     * @return the new forum mailcue
     */
    function convert_to_lcms($array)
    {
        $course = $array['course'];
    }

    /**
     * Retrieve all forum mailcues from the database
     * @param array $parameters parameters for the retrieval
     * @return array of forum mailcues
     */
    static function get_all($parameters)
    {
        self :: $mgdm = $parameters['mgdm'];
        
        $db = $parameters['course']->get_db_name();
        $tablename = 'forum_mailcue';
        $classname = 'Dokeos185ForumMailcue';
        
        return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'forum_mailcue';
        return $array;
    }
}

?>