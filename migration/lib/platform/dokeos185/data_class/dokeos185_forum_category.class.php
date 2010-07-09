<?php
/**
 * $Id: dokeos185_forum_category.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_forum_category.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/category_manager/content_object_publication_category.class.php';

/**
 * This class presents a Dokeos185 forum_category
 *
 * @author Sven Vanpoucke
 */
class Dokeos185ForumCategory extends MigrationDataClass
{
    /** 
     * Migration data manager
     */
    private static $mgdm;
    
    /**
     * Dokeos185ForumCategory properties
     */
    const PROPERTY_CAT_ID = 'cat_id';
    const PROPERTY_CAT_TITLE = 'cat_title';
    const PROPERTY_CAT_COMMENT = 'cat_comment';
    const PROPERTY_CAT_ORDER = 'cat_order';
    const PROPERTY_LOCKED = 'locked';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185ForumCategory object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185ForumCategory($defaultProperties = array ())
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
        return array(self :: PROPERTY_CAT_ID, self :: PROPERTY_CAT_TITLE, self :: PROPERTY_CAT_COMMENT, self :: PROPERTY_CAT_ORDER, self :: PROPERTY_LOCKED);
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
     * Returns the cat_id of this Dokeos185ForumCategory.
     * @return the cat_id.
     */
    function get_cat_id()
    {
        return $this->get_default_property(self :: PROPERTY_CAT_ID);
    }

    /**
     * Returns the cat_title of this Dokeos185ForumCategory.
     * @return the cat_title.
     */
    function get_cat_title()
    {
        return $this->get_default_property(self :: PROPERTY_CAT_TITLE);
    }

    /**
     * Returns the cat_comment of this Dokeos185ForumCategory.
     * @return the cat_comment.
     */
    function get_cat_comment()
    {
        return $this->get_default_property(self :: PROPERTY_CAT_COMMENT);
    }

    /**
     * Returns the cat_order of this Dokeos185ForumCategory.
     * @return the cat_order.
     */
    function get_cat_order()
    {
        return $this->get_default_property(self :: PROPERTY_CAT_ORDER);
    }

    /**
     * Returns the locked of this Dokeos185ForumCategory.
     * @return the locked.
     */
    function get_locked()
    {
        return $this->get_default_property(self :: PROPERTY_LOCKED);
    }

    /**
     * Check if the forum category is valid
     * @param array $array the parameters for the validation
     * @return true if the forum category is valid 
     */
    function is_valid($array)
    {
        $course = $array['course'];
        $mgdm = MigrationDataManager :: get_instance();
        if (! ($this->get_cat_title() || $this->get_cat_comment()))
        {
            $mgdm->add_failed_element($this->get_cat_id(), $course->get_db_name() . '.forum_category');
            return false;
        }
        
        return true;
    }

    /**
     * Convert to new forum category
     * @param array $array the parameters for the conversion
     * @return the new forum category
     */
    function convert_data
    {
        //Course category parameters
        $mgdm = MigrationDataManager :: get_instance();
        $lcms_forum_category = new ContentObjectPublicationCategory();
        $course = $array['course'];
        
        if ($this->get_cat_title())
            $lcms_forum_category->set_name($this->get_cat_title());
        else
            $lcms_forum_category->set_name($this->get_cat_comment());
        
        $old_id = $this->get_cat_id();
        
        $lcms_forum_category->set_course($mgdm->get_id_reference($course->get_code(), 'weblcms_course'));
        
        $lcms_forum_category->set_tool('forum');
        
        //create course_category in database
        $lcms_forum_category->create();
        
        //Add id references to temp table
        $mgdm->add_id_reference($old_id, $lcms_forum_category->get_id(), 'weblcms_content_object_publication_category');
        
        return $lcms_forum_category;
    }

    /**
     * Retrieve all forum categories from the database
     * @param array $parameters parameters for the retrieval
     * @return array of forum categories
     */
    static function retrieve_data($parameters)
    {
        $old_mgdm = $parameters['old_mgdm'];
        
        if ($parameters['del_files'] = ! 1)
            $tool_name = 'forum_category';
        
        $coursedb = $parameters['course']->get_db_name();
        $tablename = 'forum_category';
        $classname = 'Dokeos185ForumCategory';
        
        return $old_mgdm->get_all($coursedb, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'forum_category';
        return $array;
    }
}

?>