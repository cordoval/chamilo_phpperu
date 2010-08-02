<?php

/**
 * $Id: dokeos185_link.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class represents an old Dokeos 1.8.5 course_rel_class
 *
 * @author David Van Wayenbergh
 */

class Dokeos185Link extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'link';
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
     * Default properties stored in an associative array.
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
    function is_valid()
    {
        $this->set_item_property($this->get_data_manager()->get_item_property($this->get_course(), 'link', $this->get_id()));
        
        if (! $this->get_url() || ! $this->get_id() || ! $this->get_title() || ! $this->get_item_property() || ! $this->get_item_property()->get_ref() || ! $this->get_item_property()->get_insert_date())
        {
            $this->create_failed_element($this->get_id());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'calendar_event', 'ID' => $this->get_id())));
            return false;
        }
        return true;
    }

    /**
     * Convert to new link
     * @param Course $course the course
     * @return the new link
     */
    function convert_data()
    {
        $course = $this->get_course();
        $mgdm = MigrationDataManager :: get_instance();
        $new_user_id = $this->get_id_reference($this->get_item_property()->get_insert_user_id(), 'main_database.user');
        $new_course_code = $this->get_id_reference($course->get_code(), 'main_database.course');

        //the $this->category_id is the id of the category in which the link resides in a dokeos 1.8.5 course (in chamilo: the publication category, not the repository category id!)
        $new_publication_category_id = $this->get_id_reference($this->get_category_id(), $this->get_database_name() . '.link_category' );
        
        if (! $new_user_id)
        {
            $new_user_id = $this->get_data_manager()->get_owner($new_course_code);
        }
        
        $chamilo_link = new Link();
        
        // Category for links already exists?
        $chamilo_category_id = RepositoryDataManager :: get_repository_category_by_name_or_create_new($new_user_id, Translation :: get('Links'));
        
        $chamilo_link->set_parent_id($chamilo_category_id);
        
        
        $chamilo_link->set_url($this->get_url());
        $chamilo_link->set_title($this->get_title());
        if ($this->get_description())
            $chamilo_link->set_description($this->get_description());
        else
            $chamilo_link->set_description($this->get_title());
        
        $chamilo_link->set_owner_id($new_user_id);
        $chamilo_link->set_creation_date(strtotime($this->get_item_property()->get_insert_date()));
        $chamilo_link->set_modification_date(strtotime($this->get_item_property()->get_lastedit_date()));
        
        if ($this->get_item_property()->get_visibility() == 2)
            $chamilo_link->set_state(1);
            
        //create link in database
        $chamilo_link->create_all();
        
//        //Add id references to temp table
//        $mgdm->add_id_reference($this->get_id(), $lcms_link->get_id(), 'repository_link');
        
        //publication

        $this->create_publication($chamilo_link, $new_course_code, $new_user_id, 'link', $new_publication_category_id);

        $this->set_message(Translation :: get('GeneralConvertedMessage', array('TYPE' => 'link', 'OLD_ID' => $this->get_id(), 'NEW_ID' => $chamilo_link->get_id())));
        return $chamilo_link;
        
    }
    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }

    static function get_class_name()
    {
    	return self :: CLASS_NAME;
    }
}
?>