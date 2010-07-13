<?php
/**
 * $Id: dokeos185_dropbox_category.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_dropbox_category.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/category_manager/content_object_publication_category.class.php';

/**
 * This class presents a Dokeos185 dropbox_category
 *
 * @author Sven Vanpoucke
 */
class Dokeos185DropboxCategory extends Dokeos185MigrationDataClass
{
    private static $mgdm;
    
    /**
     * Dokeos185DropboxCategory properties
     */
    const PROPERTY_CAT_ID = 'cat_id';
    const PROPERTY_CAT_NAME = 'cat_name';
    const PROPERTY_RECEIVED = 'received';
    const PROPERTY_SENT = 'sent';
    const PROPERTY_USER_ID = 'user_id';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185DropboxCategory object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185DropboxCategory($defaultProperties = array ())
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
        return array(self :: PROPERTY_CAT_ID, self :: PROPERTY_CAT_NAME, self :: PROPERTY_RECEIVED, self :: PROPERTY_SENT, self :: PROPERTY_USER_ID);
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
     * Returns the cat_id of this Dokeos185DropboxCategory.
     * @return the cat_id.
     */
    function get_cat_id()
    {
        return $this->get_default_property(self :: PROPERTY_CAT_ID);
    }

    /**
     * Returns the cat_name of this Dokeos185DropboxCategory.
     * @return the cat_name.
     */
    function get_cat_name()
    {
        return $this->get_default_property(self :: PROPERTY_CAT_NAME);
    }

    /**
     * Returns the received of this Dokeos185DropboxCategory.
     * @return the received.
     */
    function get_received()
    {
        return $this->get_default_property(self :: PROPERTY_RECEIVED);
    }

    /**
     * Returns the sent of this Dokeos185DropboxCategory.
     * @return the sent.
     */
    function get_sent()
    {
        return $this->get_default_property(self :: PROPERTY_SENT);
    }

    /**
     * Returns the user_id of this Dokeos185DropboxCategory.
     * @return the user_id.
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Sets the code of this category.
     * @param String $code The code.
     */
    function set_cat_id($code)
    {
        $this->set_default_property(self :: PROPERTY_CAT_ID, $code);
    }

    /**
     * Check if the dropbox category is valid
     * @param array $array the parameters for the validation
     * @return true if the dropbox category is valid
     */
    function is_valid($array)
    {
        $course = $array['course'];
        $mgdm = MigrationDataManager :: get_instance();
        if (! $this->get_cat_name())
        {
            $mgdm->add_failed_element($this->get_cat_id(), $course->get_db_name() . '.dropbox_category');
            return false;
        }
        
        return true;
    }

    /**
     * Convert to new dropbox category
     * @param array $array the parameters for the conversion
     * @return the new dropbox category
     */
    function convert_data
    {
        $mgdm = MigrationDataManager :: get_instance();
        //Course category parameters
        $lcms_dropbox_category = new ContentObjectPublicationCategory();
        $course = $array['course'];
        $lcms_dropbox_category->set_name($this->get_cat_name());
        
        $lcms_dropbox_category->set_parent(0);
        
        $lcms_dropbox_category->set_course($mgdm->get_id_reference($course->get_code(), 'weblcms_course'));
        
        $lcms_dropbox_category->set_tool('dropbox');
        
        //create course_category in database
        $lcms_dropbox_category->create();
        
        //Add id references to temp table
        $mgdm->add_id_reference($this->get_cat_id(), $lcms_dropbox_category->get_id(), 'weblcms_content_object_publication_category');
        
        return $lcms_dropbox_category;
    }

    /**
     * Retrieve all dropbox categories from the database
     * @param array $parameters parameters for the retrieval
     * @return array of dropbox categories
     */
    static function retrieve_data($parameters)
    {
        $old_mgdm = $parameters['old_mgdm'];
        
        $coursedb = $parameters['course']->get_db_name();
        $tablename = 'dropbox_category';
        $classname = 'Dokeos185DropboxCategory';
        
        return $old_mgdm->get_all($coursedb, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'dropbox_category';
        return $array;
    }
}

?>