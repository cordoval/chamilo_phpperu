<?php
/**
 * $Id: dokeos185_blog_comment.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_blog_comment.class.php';

/**
 * This class presents a Dokeos185 blog_comment
 *
 * @author Sven Vanpoucke
 */
class Dokeos185BlogComment extends MigrationDataClass
{
    private static $mgdm;
    
    /**
     * Dokeos185BlogComment properties
     */
    const PROPERTY_COMMENT_ID = 'comment_id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_COMMENT = 'comment';
    const PROPERTY_AUTHOR_ID = 'author_id';
    const PROPERTY_DATE_CREATION = 'date_creation';
    const PROPERTY_BLOG_ID = 'blog_id';
    const PROPERTY_POST_ID = 'post_id';
    const PROPERTY_TASK_ID = 'task_id';
    const PROPERTY_PARENT_COMMENT_ID = 'parent_comment_id';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185BlogComment object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185BlogComment($defaultProperties = array ())
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
        return array(self :: PROPERTY_COMMENT_ID, self :: PROPERTY_TITLE, self :: PROPERTY_COMMENT, self :: PROPERTY_AUTHOR_ID, self :: PROPERTY_DATE_CREATION, self :: PROPERTY_BLOG_ID, self :: PROPERTY_POST_ID, self :: PROPERTY_TASK_ID, self :: PROPERTY_PARENT_COMMENT_ID);
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
     * Returns the comment_id of this Dokeos185BlogComment.
     * @return the comment_id.
     */
    function get_comment_id()
    {
        return $this->get_default_property(self :: PROPERTY_COMMENT_ID);
    }

    /**
     * Returns the title of this Dokeos185BlogComment.
     * @return the title.
     */
    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * Returns the comment of this Dokeos185BlogComment.
     * @return the comment.
     */
    function get_comment()
    {
        return $this->get_default_property(self :: PROPERTY_COMMENT);
    }

    /**
     * Returns the author_id of this Dokeos185BlogComment.
     * @return the author_id.
     */
    function get_author_id()
    {
        return $this->get_default_property(self :: PROPERTY_AUTHOR_ID);
    }

    /**
     * Returns the date_creation of this Dokeos185BlogComment.
     * @return the date_creation.
     */
    function get_date_creation()
    {
        return $this->get_default_property(self :: PROPERTY_DATE_CREATION);
    }

    /**
     * Returns the blog_id of this Dokeos185BlogComment.
     * @return the blog_id.
     */
    function get_blog_id()
    {
        return $this->get_default_property(self :: PROPERTY_BLOG_ID);
    }

    /**
     * Returns the post_id of this Dokeos185BlogComment.
     * @return the post_id.
     */
    function get_post_id()
    {
        return $this->get_default_property(self :: PROPERTY_POST_ID);
    }

    /**
     * Returns the task_id of this Dokeos185BlogComment.
     * @return the task_id.
     */
    function get_task_id()
    {
        return $this->get_default_property(self :: PROPERTY_TASK_ID);
    }

    /**
     * Returns the parent_comment_id of this Dokeos185BlogComment.
     * @return the parent_comment_id.
     */
    function get_parent_comment_id()
    {
        return $this->get_default_property(self :: PROPERTY_PARENT_COMMENT_ID);
    }

    /**
     * Check if the blog comment is valid
     * @param array $array the parameters for the validation
     */
    function is_valid($array)
    {
        $course = $array['course'];
    }

    /**
     * Convert to new blog comment
     * @param array $array the parameters for the conversion
     */
    function convert_data
    {
        $course = $array['course'];
    }

    /**
     * Retrieve all blog comments from the database
     * @param array $parameters parameters for the retrieval
     */
    static function retrieve_data($parameters)
    {
        self :: $mgdm = $parameters['mgdm'];
        
        $db = $parameters['course']->get_db_name();
        $tablename = 'blog_comment';
        $classname = 'Dokeos185BlogComment';
        
        return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'blog_comment';
        return $array;
    }
}

?>