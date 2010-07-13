<?php

/**
 * $Id: dokeos185_announcement.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_announcement.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/content_object/announcement/announcement.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/content_object_publication.class.php';
require_once 'dokeos185_item_property.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/category_manager/repository_category.class.php';

/**
 * This class represents an old Dokeos 1.8.5 announcement
 *
 * @author Sven Vanpoucke
 */
class Dokeos185Announcement extends Dokeos185MigrationDataClass
{
    /**
     * Migration data manager
     */
    private static $mgdm;
    private $item_property;
    
    /**
     * Announcement properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_CONTENT = 'content';
    const PROPERTY_END_DATE = 'end_date';
    const PROPERTY_DISPLAY_ORDER = 'display_order';
    const PROPERTY_EMAIL_SENT = 'email_sent';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new dokeos185 Announcement object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185Announcement($defaultProperties = array ())
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
        return array(self :: PROPERTY_ID, self :: PROPERTY_TITLE, self :: PROPERTY_CONTENT, self :: PROPERTY_END_DATE, self :: PROPERTY_DISPLAY_ORDER, self :: PROPERTY_EMAIL_SENT);
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
     * Returns the id of this announcement.
     * @return int The id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the title of this announcement.
     * @return string the title.
     */
    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * Returns the content of this announcement.
     * @return string the content.
     */
    function get_content()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT);
    }

    /**
     * Returns the end_date of this announcement.
     * @return date the end_date.
     */
    function get_end_date()
    {
        return $this->get_default_property(self :: PROPERTY_END_DATE);
    }

    /**
     * Returns the display_order of this announcement.
     * @return int the display_order.
     */
    function get_display_order()
    {
        return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER);
    }

    /**
     * Returns the email_sent of this announcement.
     * @return int the email_sent.
     */
    function get_email_sent()
    {
        return $this->get_default_property(self :: PROPERTY_EMAIL_SENT);
    }

    /**
     * Check if the announcement is valid
     * @param Course $course the course to which the announcement belongs
     * @return true if the announcement is valid 
     */
    function is_valid($array)
    {
        $old_mgdm = $array['old_mgdm'];
        $course = $array['course'];
        $mgdm = MigrationDataManager :: get_instance();
        
        $this->item_property = $old_mgdm->get_item_property($course->get_db_name(), 'announcement', $this->get_id());
        
        if (! $this->get_id() || ! ($this->get_title() || $this->get_content()) || ! $this->item_property || ! $this->item_property->get_ref() || ! $this->item_property->get_insert_date())
        {
            $mgdm->add_failed_element($this->get_id(), $course->get_db_name() . '.announcement');
            return false;
        }
        return true;
    }

    /**
     * Convert to new announcement 
     * Create announcement
     * Create publication
     * @param Course $course the course to which the announcement belongs
     * @return the new announcement
     */
    function convert_data($parameters)
    {
        $course = $array['course'];
        $mgdm = MigrationDataManager :: get_instance();
        $old_mgdm = $array['old_mgdm'];
        
        $new_user_id = $mgdm->get_id_reference($this->item_property->get_insert_user_id(), 'user_user');
        $new_course_code = $mgdm->get_id_reference($course->get_code(), 'weblcms_course');
        
        if (! $new_user_id)
        {
            $new_user_id = $mgdm->get_owner($new_course_code);
        }
        
        //announcement parameters
        $lcms_announcement = new Announcement();
        
        // Category for announcements already exists?
        $lcms_category_id = $mgdm->get_parent_id($new_user_id, 'category', Translation :: get('announcements'));
        if (! $lcms_category_id)
        {
            //Create category for tool in lcms
            $lcms_repository_category = new RepositoryCategory();
            $lcms_repository_category->set_user_id($new_user_id);
            $lcms_repository_category->set_name(Translation :: get('descriptions'));
            $lcms_repository_category->set_parent(0);
            
            //Create category in database
            $lcms_repository_category->create();
            
            $lcms_announcement->set_parent_id($lcms_repository_category->get_id());
        }
        else
        {
            $lcms_announcement->set_parent_id($lcms_category_id);
        }
        
        if (! $this->get_title())
            $lcms_announcement->set_title(substr($this->get_content(), 0, 20));
        else
            $lcms_announcement->set_title($this->get_title());
        
        if (! $this->get_content())
            $lcms_announcement->set_description($this->get_title());
        else
            $lcms_announcement->set_description($this->get_content());
        
        $lcms_announcement->set_owner_id($new_user_id);
        $lcms_announcement->set_creation_date($mgdm->make_unix_time($this->item_property->get_insert_date()));
        $lcms_announcement->set_modification_date($mgdm->make_unix_time($this->item_property->get_lastedit_date()));
        
        if ($this->item_property->get_visibility() == 2)
            $lcms_announcement->set_state(1);
            
        //create announcement in database
        $lcms_announcement->create_all();
        
        //publication
        if ($this->item_property->get_visibility() <= 1)
        {
            $publication = new ContentObjectPublication();
            
            $publication->set_content_object($lcms_announcement);
            $publication->set_course_id($new_course_code);
            $publication->set_publisher_id($new_user_id);
            $publication->set_tool('announcement');
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
            
            if ($this->get_email_sent())
                $publication->set_email_sent($this->get_email_sent());
            else
                $publication->set_email_sent(0);
            
            $publication->set_hidden($this->item_property->get_visibility() == 1 ? 0 : 1);
            
            //create publication in database
            $publication->create();
        }
        
        return $lcms_announcement;
    }

    /**
     * Retrieve all announcements from the database
     * @param array $parameters parameters for the retrieval
     * @return array of announcements
     */
    static function retrieve_data($parameters)
    {
        $old_mgdm = $parameters['old_mgdm'];
        
        if ($parameters['del_files'] = ! 1)
            $tool_name = 'announcement';
        
        $coursedb = $parameters['course']->get_db_name();
        $tablename = 'announcement';
        $classname = 'Dokeos185Announcement';
        
        return $old_mgdm->get_all($coursedb, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'announcement';
        return $array;
    }
}
?>