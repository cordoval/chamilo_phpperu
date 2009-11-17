<?php
/**
 * $Id: dokeos185_dropbox_post.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_dropbox_post.class.php';

/**
 * This class presents a Dokeos185 dropbox_post
 *
 * @author Sven Vanpoucke
 */
class Dokeos185DropboxPost extends ImportDropboxPost
{
    private static $mgdm;
    
    /**
     * Dokeos185DropboxPost properties
     */
    const PROPERTY_FILE_ID = 'file_id';
    const PROPERTY_DEST_USER_ID = 'dest_user_id';
    const PROPERTY_FEEDBACK_DATE = 'feedback_date';
    const PROPERTY_FEEDBACK = 'feedback';
    const PROPERTY_CAT_ID = 'cat_id';
    const PROPERTY_SESSION_ID = 'session_id';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185DropboxPost object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185DropboxPost($defaultProperties = array ())
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
        return array(self :: PROPERTY_FILE_ID, self :: PROPERTY_DEST_USER_ID, self :: PROPERTY_FEEDBACK_DATE, self :: PROPERTY_FEEDBACK, self :: PROPERTY_CAT_ID, self :: PROPERTY_SESSION_ID);
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
     * Returns the file_id of this Dokeos185DropboxPost.
     * @return the file_id.
     */
    function get_file_id()
    {
        return $this->get_default_property(self :: PROPERTY_FILE_ID);
    }

    /**
     * Returns the dest_user_id of this Dokeos185DropboxPost.
     * @return the dest_user_id.
     */
    function get_dest_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_DEST_USER_ID);
    }

    /**
     * Returns the feedback_date of this Dokeos185DropboxPost.
     * @return the feedback_date.
     */
    function get_feedback_date()
    {
        return $this->get_default_property(self :: PROPERTY_FEEDBACK_DATE);
    }

    /**
     * Returns the feedback of this Dokeos185DropboxPost.
     * @return the feedback.
     */
    function get_feedback()
    {
        return $this->get_default_property(self :: PROPERTY_FEEDBACK);
    }

    /**
     * Returns the cat_id of this Dokeos185DropboxPost.
     * @return the cat_id.
     */
    function get_cat_id()
    {
        return $this->get_default_property(self :: PROPERTY_CAT_ID);
    }

    /**
     * Returns the session_id of this Dokeos185DropboxPost.
     * @return the session_id.
     */
    function get_session_id()
    {
        return $this->get_default_property(self :: PROPERTY_SESSION_ID);
    }

    /**
     * Check if the dropbox post is valid
     * @param array $array the parameters for the validation
     * @return true if the dropbox post is valid 
     */
    function is_valid($array)
    {
        $course = $array['course'];
    }

    /**
     * Convert to new dropbox post
     * @param array $array the parameters for the conversion
     * @return the new dropbox post
     */
    function convert_to_lcms($array)
    {
        $course = $array['course'];
    }

    /**
     * Retrieve all dropbox posts from the database
     * @param array $parameters parameters for the retrieval
     * @return array of dropbox posts
     */
    static function get_all($parameters)
    {
        self :: $mgdm = $parameters['mgdm'];
        
        $db = $parameters['course']->get_db_name();
        $tablename = 'dropbox_post';
        $classname = 'Dokeos185DropboxPost';
        
        return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'dropbox_post';
        return $array;
    }
}

?>