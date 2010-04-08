<?php

/**
 * $Id: dokeos185_link.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_link.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/content_object/link/link.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/content_object_publication.class.php';
require_once 'dokeos185_item_property.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/category_manager/repository_category.class.php';

/**
 * This class represents an old Dokeos 1.8.5 course_rel_class
 *
 * @author David Van Wayenbergh
 */

class Dokeos185Link extends ImportLink
{
    private static $mgdm;
    private $item_property;
    
    /**
     * course relation class properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_URL = 'url';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_CATEGORY_ID = 'category_id';
    const PROPERTY_DISPLAY_ORDER = 'display_order';
    const PROPERTY_ON_HOMEPAGE = 'on_homepage';
    
    /**
     * Default properties of the link object, stored in an associative
     * array.
     */
    private $defaultProperties;

    /**
     * Creates a new link object.
     * @param array $defaultProperties The default properties of the link
     *                                 object. Associative array.
     */
    function Dokeos185Link($defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets a default property of this link object by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gets the default properties of this link.
     * @return array An associative array containing the properties.
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    /**
     * Get the default properties of all links.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_URL, self :: PROPERTY_TITLE, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_CATEGORY_ID, self :: PROPERTY_DISPLAY_ORDER, self :: PROPERTY_ON_HOMEPAGE);
    }

    /**
     * Sets a default property of this link by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Sets the default properties of this link.
     * @param array $defaultProperties An associative array containing the properties.
     */
    function set_default_properties($defaultProperties)
    {
        return $this->defaultProperties = $defaultProperties;
    }

    /**
     * Returns the id of this link.
     * @return int The id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the url of this link.
     * @return String The url.
     */
    function get_url()
    {
        return $this->get_default_property(self :: PROPERTY_URL);
    }

    /**
     * Returns the title of this link.
     * @return String The title.
     */
    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * Returns the description of this link.
     * @return String The description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Returns the category_id of this link.
     * @return int The category_id.
     */
    function get_category_id()
    {
        return $this->get_default_property(self :: PROPERTY_CATEGORY_ID);
    }

    /**
     * Returns the display_order of this link.
     * @return int The display_order.
     */
    function get_display_order()
    {
        return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER);
    }

    /**
     * Returns the on_homepage of this link.
     * @return String The on_homepage.
     */
    function get_on_homepage()
    {
        return $this->get_default_property(self :: PROPERTY_ON_HOMEPAGE);
    }

    /**
     * Check if the link is valid
     * @param Course $course the course
     * @return true if the link is valid 
     */
    function is_valid($array)
    {
        $course = $array['course'];
        $mgdm = MigrationDataManager :: get_instance();
        $old_mgdm = $array['old_mgdm'];
        $this->item_property = $old_mgdm->get_item_property($course->get_db_name(), 'link', $this->get_id());
        
        if (! $this->get_url() || ! $this->get_id() || ! $this->get_title() || ! $this->item_property || ! $this->item_property->get_ref() || ! $this->item_property->get_insert_date())
        {
            $mgdm->add_failed_element($this->get_id(), $course->get_db_name() . '.link');
            return false;
        }
        return true;
    }

    /**
     * Convert to new link
     * @param Course $course the course
     * @return the new link
     */
    function convert_to_lcms($array)
    {
        $course = $array['course'];
        $mgdm = MigrationDataManager :: get_instance();
        $new_user_id = $mgdm->get_id_reference($this->item_property->get_insert_user_id(), 'user_user');
        $new_course_code = $mgdm->get_id_reference($course->get_code(), 'weblcms_course');
        
        if (! $new_user_id)
        {
            $new_user_id = $mgdm->get_owner($new_course_code);
        }
        
        $lcms_link = new Link();
        
        // Category for links already exists?
        $lcms_category_id = $mgdm->get_parent_id($new_user_id, 'category', Translation :: get('Links'));
        if (! $lcms_category_id)
        {
            //Create category for tool in lcms
            $lcms_repository_category = new RepositoryCategory();
            $lcms_repository_category->set_user_id($new_user_id);
            $lcms_repository_category->set_name(Translation :: get('Links'));
            $lcms_repository_category->set_parent(0);
            
            //Create category in database
            $lcms_repository_category->create();
            
            $lcms_link->set_parent_id($lcms_repository_category->get_id());
        }
        else
        {
            $lcms_link->set_parent_id($lcms_category_id);
        }
        
        $lcms_link->set_url($this->get_url());
        $lcms_link->set_title($this->get_title());
        if ($this->get_description())
            $lcms_link->set_description($this->get_description());
        else
            $lcms_link->set_description($this->get_title());
        
        $lcms_link->set_owner_id($new_user_id);
        $lcms_link->set_creation_date($mgdm->make_unix_time($this->item_property->get_insert_date()));
        $lcms_link->set_modification_date($mgdm->make_unix_time($this->item_property->get_lastedit_date()));
        
        if ($this->item_property->get_visibility() == 2)
            $lcms_link->set_state(1);
            
        //create link in database
        $lcms_link->create_all();
        
        //Add id references to temp table
        $mgdm->add_id_reference($this->get_id(), $lcms_link->get_id(), 'repository_link');
        
        //publication
        if ($this->item_property->get_visibility() <= 1)
        {
            $publication = new ContentObjectPublication();
            
            $publication->set_content_object($lcms_link);
            $publication->set_course_id($new_course_code);
            $publication->set_publisher_id($new_user_id);
            $publication->set_tool('link');
            
            $category_id = $mgdm->get_id_reference($this->get_category_id(), $new_course_code . '.link_category');
            if ($category_id)
                $publication->set_category_id($category_id);
            else
                $publication->set_category_id(0);
                
            //$publication->set_from_date($mgdm->make_unix_time($this->item_property->get_start_visible()));
            //$publication->set_to_date($mgdm->make_unix_time($this->item_property->get_end_visible()));
            $publication->set_from_date(0);
            $publication->set_to_date(0);
            $publication->set_publication_date($mgdm->make_unix_time($this->item_property->get_insert_date()));
            $publication->set_modified_date($mgdm->make_unix_time($this->item_property->get_lastedit_date()));
            //$publication->set_modified_date(0);
            //$publication->set_display_order_index($this->get_display_order());
            $publication->set_display_order_index(0);
            $publication->set_email_sent(0);
            
            $publication->set_hidden($this->item_property->get_visibility() == 1 ? 0 : 1);
            
            //create publication in database
            $publication->create();
        }
        
        return $lcms_link;
    
    }

    /**
     * Retrieve all links from the database
     * @param array $parameters parameters for the retrieval
     * @return array of links
     */
    static function get_all($parameters)
    {
        $old_mgdm = $parameters['old_mgdm'];
        
        if ($parameters['del_files'] = ! 1)
            $tool_name = 'link';
        
        $coursedb = $parameters['course']->get_db_name();
        $tablename = 'link';
        $classname = 'Dokeos185Link';
        
        return $old_mgdm->get_all($coursedb, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'link';
        return $array;
    }
}
?>